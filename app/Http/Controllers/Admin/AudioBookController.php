<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AudioBook;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Report;
use App\Models\ListeningHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use App\Notifications\GeneralNotification;
use App\Models\User;


class AudioBookController extends Controller
{
    /**
     * عرض جميع الكتب الصوتية مع البحث والفلترة والإحصائيات.
     */
    public function index(Request $request)
    {
        $query = AudioBook::with(['publisher', 'category'])
                          ->withCount(['reports', 'ratings'])
                          ->withAvg('ratings', 'rating');

        if ($request->filled('search')) {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        $audiobooks = $query->latest()->paginate(15)->appends($request->except('page'));
        $categories = Category::orderBy('name', 'asc')->get();

        return view('admin.audiobooks.index', [
            'audiobooks' => $audiobooks,
            'categories' => $categories,
            'search' => $request->search ?? '',
            'selectedCategory' => $request->category_id ?? '',
            'selectedStatus' => $request->status ?? ''
        ]);
    }

    /**
     * الموافقة على كتاب وإرسال إشعارات للناشر والمتابعين.
     */
    public function approve(AudioBook $audioBook)
    {
        Log::info('--- بداية عملية الموافقة على الكتاب ---');
        Log::info('معرف الكتاب المستلم: ' . $audioBook->id);
        Log::info('حالة الكتاب الحالية (قبل التحديث): ' . $audioBook->status);

        if ($audioBook->status === 'approved') {
            Log::warning('الكتاب معتمد بالفعل. تم إيقاف العملية.');
            return response()->json(['success' => false, 'message' => 'هذا الكتاب تمت الموافقة عليه بالفعل.']);
        }

        try {
            Log::info("محاولة تحديث حالة الكتاب إلى 'approved'...");
            $updated = $audioBook->update(['status' => 'approved']);

            if ($updated) {
                Log::info('نجاح! تم تحديث قاعدة البيانات بنجاح.');
            } else {
                Log::error('فشل! دالة update() أعادت false. لم يتم تحديث قاعدة البيانات.');
                return response()->json(['success' => false, 'message' => 'فشل تحديث حالة الكتاب في قاعدة البيانات.']);
            }

            $audioBook->refresh();
            Log::info('حالة الكتاب بعد التحديث (بعد refresh): ' . $audioBook->status);

            $publisher = $audioBook->publisher;

            if ($publisher) {
                Log::info("جاري إرسال إشعار للناشر: {$publisher->name}");
                $publisherNotificationData = [
                    'message' => "تمت الموافقة على كتابك: '{$audioBook->title}' وهو الآن متاح على المنصة.",
                    'link'    => route('publisher.audio-books.index'),
                    'icon'    => 'fas fa-check-circle'
                ];
                $publisher->notify(new GeneralNotification($publisherNotificationData));
                Log::info("تم إرسال الإشعار للناشر بنجاح.");

                Log::info("جاري البحث عن مشتركين للناشر: {$publisher->name}");
                $subscribers = $publisher->subscribers;
                Log::info("تم العثور على " . $subscribers->count() . " مشترك.");

                if ($subscribers->isNotEmpty()) {
                    Log::info("جاري تجهيز وإرسال الإشعارات للمشتركين...");
                    $subscriberNotificationData = [
                        'message' => "الناشر '{$publisher->name}' نشر كتاباً جديداً: '{$audioBook->title}'",
                        'link'    => route('listener.audiobook.show', $audioBook->id),
                        'icon'    => 'fas fa-book-reader'
                    ];
                    Notification::send($subscribers, new GeneralNotification($subscriberNotificationData));
                    Log::info("تم إرسال الإشعارات للمشتركين بنجاح.");
                }
            }

            Log::info('--- نهاية عملية الموافقة بنجاح ---');
            return response()->json([
                'success' => true,
                'message' => 'تمت الموافقة بنجاح وإرسال الإشعارات!',
                'book_id' => $audioBook->id
            ]);

        } catch (\Exception $e) {
            Log::error('!!! حدث خطأ استثنائي أثناء عملية الموافقة !!!');
            Log::error('رسالة الخطأ: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'حدث خطأ في الخادم. راجع السجلات.']);
        }
    }

    /**
     * رفض كتاب صوتي.
     */
    public function reject(AudioBook $audioBook)
    {
        Log::info("--- بداية عملية رفض الكتاب: {$audioBook->id} ---");

        if ($audioBook->status === 'rejected') {
            Log::warning('الكتاب مرفوض بالفعل. تم إيقاف العملية.');
            return response()->json(['success' => false, 'message' => 'هذا الكتاب تم رفضه بالفعل.']);
        }

        try {
            $audioBook->update(['status' => 'rejected']);
            Log::info("تم تحديث حالة الكتاب إلى 'rejected' بنجاح.");

            $publisher = $audioBook->publisher;
            if ($publisher) {
                Log::info("جاري إرسال إشعار الرفض للناشر: {$publisher->name}");
                $notificationData = [
                    'message' => "نأسف، تم رفض كتابك: '{$audioBook->title}'. يرجى مراجعة معايير النشر.",
                    'link'    => route('publisher.audio-books.index'),
                    'icon'    => 'fas fa-times-circle text-danger'
                ];
                $publisher->notify(new GeneralNotification($notificationData));
                Log::info("تم إرسال إشعار الرفض للناشر بنجاح.");
            }

            Log::info("--- نهاية عملية الرفض بنجاح ---");
            return response()->json([
                'success' => true,
                'message' => 'تم رفض الكتاب بنجاح وإرسال إشعار للناشر!'
            ]);

        } catch (\Exception $e) {
            Log::error('!!! حدث خطأ استثنائي أثناء عملية الرفض !!!');
            Log::error('رسالة الخطأ: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ في الخادم. راجع السجلات.']);
        }
    }

    /**
     * حذف كتاب بناءً على الشروط.
     */
    public function destroy(AudioBook $audioBook) // <--- تم تصحيح اسم المتغير هنا
    {
        try {
            // 1. حساب المعايير من قاعدة البيانات مباشرة
            $averageRating = $audioBook->ratings()->avg('rating'); // <--- تم تصحيح اسم المتغير هنا
            $reportCount = $audioBook->reports()->count(); // <--- تم تصحيح اسم المتغير هنا

            // 2. تطبيق الشروط
            $canBeDeleted = false;
            $reasons = [];

            if ($averageRating !== null && $averageRating < 3.0) {
                $canBeDeleted = true;
                $reasons[] = "متوسط تقييمه أقل من 3 نجوم (" . number_format($averageRating, 1) . ")";
            }
            if ($reportCount > 3) {
                $canBeDeleted = true;
                $reasons[] = "عدد الإبلاغات عليه أكثر من 3 مرات ({$reportCount})";
            }

            // 3. إذا لم تتحقق الشروط، أوقف العملية
            if (!$canBeDeleted) {
                $message = 'لا يمكن حذف الكتاب. الشروط هي: متوسط تقييم أقل من 3، أو عدد إبلاغات أكثر من 3.';
                return back()->with('error', $message)->withInput();
            }

            // 4. إذا تحققت الشروط، ابدأ عملية الحذف
            $uniqueReasons = array_unique($reasons);
            Log::info("بدء حذف الكتاب ({$audioBook->id}) للأسباب التالية: " . implode(' و ', $uniqueReasons));

            // حذف الملفات المرتبطة
            if ($audioBook->cover_image_path) {
                Storage::disk('public')->delete($audioBook->cover_image_path);
            }
            if ($audioBook->file_path) {
                Storage::disk('public')->delete($audioBook->file_path);
            }

            // حذف الكتاب من قاعدة البيانات
            $audioBook->delete(); // <--- تم تصحيح اسم المتغير هنا
            Log::info("تم حذف الكتاب بنجاح.");

            // إرسال إشعار للناشر
            if ($publisher = $audioBook->publisher) { // <--- تم تصحيح اسم المتغير هنا
                $publisher->notify(new GeneralNotification([
                    'message' => "تم حذف كتابك: '{$audioBook->title}' بسبب: " . implode(' و ', $uniqueReasons),
                    'link'    => route('publisher.audio-books.index'),
                    'icon'    => 'fas fa-trash text-danger'
                ]));
            }

            return back()->with('success', 'تم حذف الكتاب الصوتي بنجاح!');

        } catch (\Exception $e) {
            Log::error("خطأ أثناء حذف الكتاب ({$audioBook->id}): " . $e->getMessage());
            return back()->with('error', 'حدث خطأ فني أثناء محاولة الحذف.');
        }
    }

    /**
     * معاينة الكتاب.
     */
    public function preview(AudioBook $audioBook)
    {
        $audioBook->load(['comments.user', 'publisher', 'category']);
        $userRating = null;
        $playlists = collect();
        $back_route = route('admin.audiobooks.index');

        return view('listener.audiobook_detail', compact(
            'audioBook',
            'userRating',
            'playlists',
            'back_route'
        ));
    }
}
