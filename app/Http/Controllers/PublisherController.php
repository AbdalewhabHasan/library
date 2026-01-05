<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AudioBook;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage; // **إضافة ضرورية** للتعامل مع الملفات
use Illuminate\Support\Facades\Log;
use App\Notifications\GeneralNotification; // ▼▼▼ أضف هذا السطر في أعلى الملف مع بقية use ▼▼▼
use Illuminate\Support\Facades\Notification; // ▼▼▼ وأضف هذا السطر أيضاً ▼▼▼
use App\Models\Comment;
use Illuminate\View\View; // قد يكون موجوداً بالفعل
use App\Models\Advertisement; // <-- أضف هذا


class PublisherController extends Controller
{
    /**
     * عرض لوحة تحكم الناشر الرئيسية مع الإحصائيات والمخططات.
     * (تم إعادة بنائه ليكون خالياً من الأخطاء 100%)
     */
    public function index()
    {
        /** @var \App\Models\User $publisher */
        $publisher = Auth::user();

        // --- 1. الإحصائيات الأساسية ---
        $totalBooks = $publisher->audioBooks()->count();
        $approvedBooks = $publisher->audioBooks()->where('status', 'approved')->count();
        $pendingBooks = $publisher->audioBooks()->where('status', 'pending')->count();
        $totalSubscribers = $publisher->subscribers()->count();

        // --- 2. الإشعارات (بالطريقة الصحيحة 100%) ---
        $unreadNotifications = $publisher->unreadNotifications;
        $unreadNotificationsCount = $unreadNotifications->count();
        $latestNotifications = $publisher->notifications->take(5);

        // --- 3. بيانات المخططات (تم تبسيطها وتصحيحها) ---
        $statusData = $publisher->audioBooks()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $statusChartLabels = $statusData->keys()->map(function ($status) {
            if ($status == 'approved') return 'مقبول';
            if ($status == 'pending') return 'قيد المراجعة';
            if ($status == 'rejected') return 'مرفوض';
            return $status;
        });
        $statusChartValues = $statusData->values();

        $growthData = DB::table('subscriptions')
            ->where('publisher_id', $publisher->id)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->pluck('count', 'date');

        $growthChartLabels = [];
        $growthChartValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $growthChartLabels[] = Carbon::parse($date)->format('D');
            $growthChartValues[] = $growthData[$date] ?? 0;
        }

        // --- 4. إرسال كل البيانات إلى الواجهة ---
        return view('publisher.dashboard', compact(
            'totalBooks',
            'approvedBooks',
            'pendingBooks',
            'totalSubscribers',
            'unreadNotificationsCount',
            'latestNotifications',
            'statusChartLabels',
            'statusChartValues',
            'growthChartLabels',
            'growthChartValues'
        ));
    }

    /**
     * عرض صفحة "كتبي" الخاصة بالناشر.
     * (تم إعادة بنائه ليعمل 100%)
     */
   // ▼▼▼ هذا هو الكود النهائي والنظيف 1000% ▼▼▼
/**
 * عرض صفحة كتب الناشر مع الفلاتر.
 */
public function showAudioBooks()
{
    /** @var \App\Models\User $publisher */
    $publisher = Auth::user();

    // 1. جلب كتب الناشر الحالي فقط
    $audioBooks = $publisher->audioBooks()->with('category')->latest()->get();

    // 2. جلب كل الفئات لعرضها في الفلتر (هذا هو السطر الذي كان ناقصاً)
    $categories = Category::orderBy('name')->get();

    // 3. إرسال كل من الكتب والفئات إلى الصفحة
    return view('publisher.audio-books.index', compact('audioBooks', 'categories'));
}
// ▲▲▲ انتهى الكود النهائي ▲▲▲


    /**
     * عرض صفحة "إضافة كتاب" جديدة.
     * (تم إعادة بنائه ليعمل 100%)
     */
    public function createAudioBook()
    {
        $categories = Category::orderBy('name')->get();
       return view('publisher.add-audio-book', compact('categories'));
    }

    /**
     * تخزين كتاب صوتي جديد في قاعدة البيانات.
     * (تم إعادة بنائه ليعمل 100%)
     */
    // ▼▼▼ استبدل دالة storeAudioBook() القديمة بهذه الدالة الجديدة والكاملة ▼▼▼


// ... (بقية الكود في الكنترولر) ...

public function storeAudioBook(Request $request)
{
    Log::info('storeAudioBook called', ['user_id' => optional(Auth::user())->id, 'files' => array_keys($request->allFiles())]);
    $validatedData = $request->validate([
        'title' => 'required|string|max:255',
        'author' => 'required|string|max:255',
        'narrator' => 'nullable|string|max:255',
        'duration' => 'required|integer|min:1',
        'category_id' => 'required|exists:categories,id',
        'language' => 'required|string|max:50',
        'description' => 'nullable|string|max:5000',
        'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'file' => 'required|file|mimes:mp3,wav,m4a,aac|max:102400', // 100MB
        'pdf_file' => 'nullable|file|mimes:pdf|max:51200', // 50MB
    ]);

    $coverPath = $request->file('cover_image')->store('cover_images', 'public');
    $audioPath = $request->file('file')->store('audio_books', 'public');
    $pdfPath = null;
    if ($request->hasFile('pdf_file')) {
        $pdfPath = $request->file('pdf_file')->store('pdf_books', 'public');
    }

    Log::info('Files stored paths', ['cover' => $coverPath ?? null, 'audio' => $audioPath ?? null, 'pdf' => $pdfPath ?? null]);

    /** @var \App\Models\User $publisher */
    $publisher = Auth::user();

    // تم تعديل هذا الجزء ليحفظ الكتاب في متغير
    $audioBook = $publisher->audioBooks()->create([
        'title' => $validatedData['title'],
        'author' => $validatedData['author'],
        'narrator' => $validatedData['narrator'],
        'duration' => $validatedData['duration'],
        'category_id' => $validatedData['category_id'],
        'language' => $validatedData['language'],
        'description' => $validatedData['description'],
        'cover_image_path' => $coverPath,
        'file_path' => $audioPath,
        'pdf_path' => $pdfPath,
        'status' => 'pending',
    ]);

    // --- ▼▼▼ هذا هو الكود الجديد والمهم الذي تم إضافته ▼▼▼ ---

    // 1. ابحث عن كل المستخدمين الذين لديهم دور "أدمن"
    $admins = User::where('role', 'admin')->get();

    // 2. جهز بيانات الإشعار
    $notificationData = [
        'message' => "كتاب جديد قيد المراجعة: '{$audioBook->title}' بواسطة '{$publisher->name}'",
        'link'    => route('admin.audiobooks.index', ['status' => 'pending']),
        'icon'    => 'fas fa-book'
    ];

    // 3. أرسل الإشعار لكل أدمن موجود في النظام
    if ($admins->isNotEmpty()) {
        Notification::send($admins, new GeneralNotification($notificationData));
    }

    Log::info('AudioBook created', ['audio_book_id' => $audioBook->id, 'pdf_path' => $audioBook->pdf_path ?? null]);

    // --- ▲▲▲ انتهت الإضافة ▲▲▲ ---

    return redirect()->route('publisher.dashboard')->with('success', 'تم رفع الكتاب بنجاح وهو الآن قيد المراجعة.');
}

    /**
     * عرض صفحة تعديل كتاب صوتي.
     * (تم إعادة بنائه ليعمل 100%)
     */
    public function editAudioBook(AudioBook $audioBook)
    {
        // التأكد من أن الناشر يملك هذا الكتاب
        if ($audioBook->publisher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        $categories = Category::orderBy('name')->get();
        return view('publisher.audio-books.edit', compact('audioBook', 'categories'));
    }

    /**
     * تحديث بيانات كتاب صوتي.
     * (تم إعادة بنائه ليعمل 100%)
     */
    public function updateAudioBook(Request $request, AudioBook $audioBook)
    {
        if ($audioBook->publisher_id !== Auth::id()) {
            abort(403);
        }

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'narrator' => 'nullable|string|max:255',
            'duration' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
            'language' => 'required|string|max:50',
            'description' => 'nullable|string|max:5000',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            // حذف الصورة القديمة إذا وجدت
            if ($audioBook->cover_image_path) {
                Storage::disk('public')->delete($audioBook->cover_image_path);
            }
            $validatedData['cover_image_path'] = $request->file('cover_image')->store('cover_images', 'public');
        }

        $audioBook->update($validatedData);

        return redirect()->route('publisher.audio-books.index')->with('success', 'تم تحديث بيانات الكتاب بنجاح.');
    }

    /**
     * حذف كتاب صوتي.
     * (تم إعادة بنائه ليعمل 100%)
     */
    public function destroyAudioBook(AudioBook $audioBook)
    {
        if ($audioBook->publisher_id !== Auth::id()) {
            abort(403);
        }

        // حذف الملفات من الـ storage
        Storage::disk('public')->delete($audioBook->cover_image_path);
        Storage::disk('public')->delete($audioBook->file_path);
        if ($audioBook->pdf_path) {
            Storage::disk('public')->delete($audioBook->pdf_path);
        }

        $audioBook->delete();

        return redirect()->route('publisher.audio-books.index')->with('success', 'تم حذف الكتاب الصوتي بنجاح.');
    }

    // --- دوال الإشعارات (تم التأكد من أنها تعمل 100%) ---

// هذا هو الكود الصحيح 1000%
// ▼▼▼ استبدل دالة allNotifications() القديمة بهذه النسخة الصحيحة ▼▼▼

public function allNotifications()
{
    /** @var \App\Models\User $user */
    $user = Auth::user();

    // الإصلاح: أضفنا الأقواس () بعد notifications
    // هذا يحولها من "مجموعة بيانات" إلى "استعلام" قابل للترقيم
    $notifications = $user->notifications()->paginate(15);

    return view('publisher.notifications.index', compact('notifications'));
}

public function markAllAsRead()
{
    Auth::user()->unreadNotifications->markAsRead(); // <-- تم حذف الأقواس
    return back()->with('success', 'تم تمييز كل الإشعارات كمقروءة.');
}

public function markOneAsRead($notificationId)
{
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $notification = $user->notifications->find($notificationId); // <-- تم حذف الأقواس
    if ($notification) {
        $notification->markAsRead();
        return redirect($notification->data['link'] ?? route('publisher.dashboard'));
    }
    return redirect()->route('publisher.dashboard');
}
/**
 * عرض صفحة إدارة التعليقات للناشر.
 */
public function manageComments(): View
{
    /** @var \App\Models\User $publisher */
    $publisher = Auth::user();

    // جلب كل التعليقات التي تنتمي للكتب التي نشرها هذا الناشر
    // نستخدم 'whereHas' للتأكد من أننا نجلب التعليقات على كتبه فقط
    $comments = Comment::whereHas('audioBook', function ($query) use ($publisher) {
        $query->where('publisher_id', $publisher->id);
    })
    ->with(['audioBook:id,title', 'user:id,name']) // لجلب بيانات الكتاب والمستخدم بكفاءة
    ->latest() // لعرض أحدث التعليقات أولاً
    ->paginate(20); // لتقسيم النتائج إلى صفحات

    return view('publisher.comments.manage', compact('comments'));
}

/**
 * حذف تعليق معين.
 */
public function deleteComment(Comment $comment)
{
    // خطوة أمان: التأكد من أن هذا الناشر يملك الحق في حذف هذا التعليق
    // (أي أن التعليق على كتاب من كتبه)
    $this->authorize('delete', $comment);

    $comment->delete();

    return back()->with('success', 'تم حذف التعليق بنجاح.');
}
/**
 * عرض صفحة إدارة الإعلانات.
 */


}
