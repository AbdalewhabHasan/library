<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // <-- أضف هذا السطر لتسجيل الأخطاء

class PaymentController extends Controller
{
    /**
     * عرض صفحة اختيار خطة الاشتراك.
     */
    public function showSubscriptionPage()
    {
        $plans = Plan::where('is_active', true)->orderBy('price')->get();
        return view('subscribe.page', compact('plans'));
    }

    /**
     * معالجة طلب الاشتراك وإنشاء جلسة دفع في Stripe.
     */
    public function processSubscription(Request $request)
    {
        $request->validate(['plan_id' => 'required|exists:plans,id']);
        $plan = Plan::find($request->plan_id);
        $user = Auth::user();

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $plan->name,
                        'description' => $plan->description,
                    ],
                    'unit_amount' => $plan->price * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('subscribe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('subscribe.cancel'),
            'metadata' => [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ]
        ]);

        return redirect($checkout_session->url);
    }

    /**
     * صفحة النجاح بعد إتمام الدفع.
     */
    public function subscriptionSuccess(Request $request)
    {
        // يمكنك هنا إضافة رسالة أكثر تفصيلاً أو توجيه المستخدم لصفحة خاصة
        return "<h1>شكراً لك!</h1><p>لقد تمت عملية الدفع بنجاح. تم تفعيل اشتراكك.</p><a href='" . route('listener.dashboard') . "'>العودة للوحة التحكم</a>";
    }

    /**
     * صفحة الإلغاء إذا تراجع المستخدم عن الدفع.
     */
    public function subscriptionCancelled()
    {
        return "<h1>تم إلغاء العملية</h1><p>لقد قمت بإلغاء عملية الدفع. يمكنك المحاولة مرة أخرى في أي وقت.</p><a href='" . route('subscribe.page') . "'>العودة لصفحة الخطط</a>";
    }

    /**
     * ▼▼▼ هذه هي الدالة الجديدة والمهمة جداً ▼▼▼
     * استقبال ومعالجة إشعارات Webhook من Stripe.
     */
    public function handleWebhook(Request $request)
    {
        // 1. إعداد مفتاح الـ Webhook السري
        $webhookSecret = env('STRIPE_WEBHOOK_SECRET');
        if (!$webhookSecret) {
            Log::error('Stripe webhook secret is not set.');
            return response()->json(['error' => 'Webhook secret not configured'], 500);
        }
        
        // 2. التحقق من صحة توقيع الإشعار (للتأكد من أنه قادم من Stripe)
        try {
            $event = \Stripe\Webhook::constructEvent(
                $request->getContent(),
                $request->header('stripe-signature'),
                $webhookSecret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe Webhook Error: Invalid payload.');
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Stripe Webhook Error: Invalid signature.');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // 3. معالجة الحدث بناءً على نوعه
        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;

            $userId = $session->metadata->user_id;
            $planId = $session->metadata->plan_id;

            $user = User::find($userId);
            $plan = Plan::find($planId);

            if ($user && $plan) {
                // ▼▼▼ هنا يتم تفعيل الاشتراك فعلياً ▼▼▼
                $user->plan_id = $plan->id;
                // حساب تاريخ انتهاء الاشتراك بناءً على مدة الخطة
                $user->subscription_ends_at = now()->addDays($plan->duration_in_days);
                $user->save();
                
                Log::info("Subscription activated for user {$user->id} with plan {$plan->id}.");
            } else {
                Log::error("Stripe Webhook: User or Plan not found.", ['user_id' => $userId, 'plan_id' => $planId]);
            }
        }

        // 4. إرسال رد ناجح إلى Stripe لإخبارهم بأننا استلمنا الإشعار
        return response()->json(['status' => 'success']);
    }
}
