<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // تأكد من وجود هذا السطر لاستخدام دالة slug

class PlanController extends Controller
{
    /**
     * عرض صفحة إدارة الخطط مع كل الخطط الموجودة.
     */
    public function index()
    {
        // جلب كل الخطط من قاعدة البيانات، مع ترتيب الأحدث أولاً وتقسيمها إلى صفحات
        $plans = Plan::latest()->paginate(10); 

        // إرسال البيانات إلى واجهة العرض
        return view('admin.plans.index', compact('plans'));
    }

    /**
     * عرض نموذج إنشاء خطة جديدة.
     */
    public function create()
    {
        // هذه الدالة بسيطة، مهمتها فقط عرض واجهة نموذج الإضافة
        return view('admin.plans.create');
    }

    /**
     * تخزين الخطة الجديدة التي تم إدخالها في قاعدة البيانات.
     */
    public function store(Request $request)
    {
        // 1. التحقق من صحة البيانات المدخلة والتأكد من أن الاسم فريد
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:plans,name',
            'price' => 'required|numeric|min:0',
            'duration_in_days' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        // 2. إنشاء slug تلقائياً من الاسم ليكون سهل القراءة في الروابط
        $validatedData['slug'] = Str::slug($validatedData['name']);
        
        // 3. التأكد من أن قيمة is_active موجودة (checkbox لا ترسل قيمة إذا لم يتم تحديدها)
        $validatedData['is_active'] = $request->has('is_active');

        // 4. إنشاء الخطة الجديدة في قاعدة البيانات باستخدام البيانات التي تم التحقق منها
        Plan::create($validatedData);

        // 5. إعادة التوجيه إلى صفحة إدارة الخطط مع رسالة نجاح
        return redirect()->route('admin.plans.index')->with('success', 'تمت إضافة الخطة بنجاح!');
    }

    /**
     * عرض نموذج تعديل الخطة مع ملء البيانات الحالية.
     * Laravel يقوم بجلب الخطة تلقائياً من قاعدة البيانات باستخدام الـ ID الموجود في الرابط.
     */
    public function edit(Plan $plan)
    {
        // إرسال بيانات الخطة المحددة إلى واجهة التعديل
        return view('admin.plans.edit', compact('plan'));
    }

    /**
     * تحديث بيانات الخطة في قاعدة البيانات بعد إرسال نموذج التعديل.
     */
    public function update(Request $request, Plan $plan)
    {
        // 1. التحقق من صحة البيانات، مع تجاهل شرط التفرد للاسم الحالي لهذه الخطة
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:plans,name,' . $plan->id,
            'price' => 'required|numeric|min:0',
            'duration_in_days' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        // 2. تحديث الـ slug إذا تم تغيير الاسم
        $validatedData['slug'] = Str::slug($validatedData['name']);

        // 3. تحديث حالة التفعيل
        $validatedData['is_active'] = $request->has('is_active');

        // 4. تحديث بيانات الخطة في قاعدة البيانات
        $plan->update($validatedData);

        // 5. إعادة التوجيه إلى صفحة إدارة الخطط مع رسالة نجاح
        return redirect()->route('admin.plans.index')->with('success', 'تم تحديث الخطة بنجاح!');
    }

    /**
     * حذف الخطة المحددة من قاعدة البيانات.
     */
    public function destroy(Plan $plan)
    {
        // حذف الخطة
        $plan->delete();

        // إعادة التوجيه إلى صفحة إدارة الخطط مع رسالة نجاح
        return redirect()->route('admin.plans.index')->with('success', 'تم حذف الخطة بنجاح!');
    }
}
