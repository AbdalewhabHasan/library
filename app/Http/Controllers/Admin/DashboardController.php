<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AudioBook;
use App\Models\Rating;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * عرض لوحة تحكم الأدمن الرئيسية.
     * (النسخة الكاملة والنهائية 1000%)
     */
    public function index()
{
    // تحديد مدة التخزين المؤقت بالدقائق (مثلاً, 30 دقيقة)
    $cacheDuration = 30;

    // --- 1. الإحصائيات الأساسية (من الكاش) ---
    $stats = Cache::remember('admin_stats', $cacheDuration, function () {
        return [
            'totalUsers' => User::count(),
            'totalAudioBooks' => AudioBook::count(),
            'pendingAudioBooks' => AudioBook::where('status', 'pending')->count(),
            'averageRating' => number_format(Rating::avg('rating'), 1),
        ];
    });

    // --- 2. النشاط الأسبوعي (من الكاش) ---
    $weeklyActivity = Cache::remember('admin_weekly_activity', $cacheDuration, function () {
        return [
            'newUsers' => User::where('created_at', '>=', Carbon::now()->subWeek())->count(),
            'newBooks' => AudioBook::where('created_at', '>=', Carbon::now()->subWeek())->count(),
        ];
    });

    // --- 3. القوائم الذكية (من الكاش) ---
    $popularAudioBooks = Cache::remember('admin_popular_books', $cacheDuration, function () {
        return AudioBook::with('publisher')->withAvg('ratings', 'rating')->orderByDesc('ratings_avg_rating')->where('status', 'approved')->limit(5)->get();
    });

    $activePublishers = Cache::remember('admin_active_publishers', $cacheDuration, function () {
        return User::where('role', 'publisher')->withCount('audioBooks')->orderByDesc('audio_books_count')->limit(5)->get();
    });

    $recentActivities = Cache::remember('admin_recent_activities', $cacheDuration, function () {
        $latestUsers = User::select('id', 'name', 'role', 'created_at')->where('role', '!=', 'admin')->latest()->limit(5);
        $latestAudioBooks = AudioBook::select('id', 'title', 'publisher_id', 'created_at')->with('publisher:id,name')->latest()->limit(5);
        return $latestUsers->get()->map(fn($user) => tap($user, fn($u) => $u->type = 'user_registration'))
            ->concat($latestAudioBooks->get()->map(fn($book) => tap($book, fn($b) => $b->type = 'book_upload')))
            ->sortByDesc('created_at')->take(5);
    });

    // --- 4. الإشعارات (هذه خاصة بالمستخدم، لا نضعها في الكاش العام) ---
    $admin = Auth::user();
    $unreadNotificationsCount = $admin->unreadNotifications->count();
    $notifications = $admin->notifications->take(5);

    // --- 5. رسم توزيع الأدوار (من الكاش) ---
    $roleChartData = Cache::remember('admin_role_chart', $cacheDuration, function () {
        $rolesData = User::select('role', DB::raw('count(*) as count'))->whereIn('role', ['listener', 'publisher'])->groupBy('role')->pluck('count', 'role');
        return [
            'labels' => $rolesData->keys()->map(fn($role) => $role === 'listener' ? 'مستمعون' : 'ناشرون')->toArray(),
            'values' => $rolesData->values()->toArray(),
        ];
    });
    $roleChartLabels = $roleChartData['labels'];
    $roleChartValues = $roleChartData['values'];


    // --- 6. إرسال كل البيانات إلى الواجهة ---
    return view('admin.dashboard', compact(
        'stats',
        'weeklyActivity',
        'roleChartLabels',
        'roleChartValues',
        'popularAudioBooks',
        'activePublishers',
        'recentActivities',
        'notifications',
        'unreadNotificationsCount'
    ));
}


    // ======================================================================
    // ==   ▼▼▼ الدوال القديمة والمهمة التي تم إرجاعها ▼▼▼               ==
    // ======================================================================

    /**
     * عرض جميع إشعارات الأدمن مع ترقيم الصفحات.
     */
    public function allNotifications()
    {
        /** @var \App\Models\User $admin */
        $admin = Auth::user();
        // **إصلاح:** تم استخدام العلاقة الصحيحة للإشعارات
        $notifications = $admin->notifications()->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * تمييز كل الإشعارات غير المقروءة الخاصة بالأدمن كمقروءة.
     */
    public function markAllAsRead()
    {
        /** @var \App\Models\User $admin */
        $admin = Auth::user();
        // **إصلاح:** تم استخدام العلاقة الصحيحة للإشعارات
        $admin->unreadNotifications->markAsRead();
        return back()->with('success', 'تم تمييز كل الإشعارات كمقروءة بنجاح.');
    }

}
