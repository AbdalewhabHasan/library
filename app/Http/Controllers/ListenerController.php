<?php

namespace App\Http\Controllers;

use App\Models\AudioBook;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Download;
use App\Models\ListeningHistory;
use App\Models\Playlist;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Services\AchievementService;
class ListenerController extends Controller
{
    /**
     * Display the listener's dashboard with audiobooks, recommendations, and other data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
  public function index(Request $request): View
{
    /** @var \App\Models\User $listener */
    $listener = Auth::user();

    // --- 1. Fetch approved audiobooks with search and filter ---
    $query = AudioBook::where('status', 'approved');
    if ($request->filled('filterType') && $request->filled('filterValue')) {
        $type = $request->filterType;
        $value = $request->filterValue;
        if ($type === 'category') {
            $query->whereHas('category', fn($q) => $q->where('name', 'like', "%{$value}%"));
        } elseif ($type === 'publisher') {
            $query->whereHas('publisher', fn($q) => $q->where('name', 'like', "%{$value}%"));
        } else if (in_array($type, ['title', 'author', 'narrator', 'language'])) {
            $query->where($type, 'like', "%{$value}%");
        }
    }
    $audioBooks = $query->latest()->paginate(10)->appends($request->query());

    // --- 2. Fetch other UI data ---
    $playlists = $listener->playlists()->get();
    $unreadNotificationsCount = $listener->unreadNotifications()->count();
    $latestNotifications = $listener->notifications()->take(5)->get();
    $bookmark = $listener->bookmarks()->with('audioBook')->latest('updated_at')->first();

    // --- 3. Fetch "Continue Listening" data ---
    $continueListening = DB::table('listening_histories')
        ->join('audio_books', 'listening_histories.audio_book_id', '=', 'audio_books.id')
        ->leftJoin('bookmarks', fn($join) => $join->on('audio_books.id', '=', 'bookmarks.audio_book_id')->where('bookmarks.listener_id', '=', $listener->id))
        ->where('listening_histories.listener_id', $listener->id)
        ->whereNotNull('audio_books.duration')->where('audio_books.duration', '>', 0)
        ->select('audio_books.id', 'audio_books.title', 'audio_books.cover_image_path', 'audio_books.duration', 'bookmarks.time as listening_progress')
        ->orderByDesc('listening_histories.listened_at')->limit(10)->get();

    // =================================================================
    // ▼▼▼ بداية نظام التوصيات الجديد والأكثر مرونة ▼▼▼
    // =================================================================
    $recommendations = [];
    $listenedBookIds = $listener->listeningHistory()->pluck('audio_book_id');

    // --- المحور الأول: التوصية بناءً على آخر فئات تم الاستماع إليها ---
    $recentCategories = DB::table('listening_histories')
        ->join('audio_books', 'listening_histories.audio_book_id', '=', 'audio_books.id')
        ->where('listening_histories.listener_id', $listener->id)
        ->whereNotNull('audio_books.category_id')
        ->select('audio_books.category_id')
        ->distinct() // جلب الفئات المختلفة بدون تكرار
        ->latest('listening_histories.listened_at') // ترتيبها حسب آخر استماع
        ->limit(2) // نكتفي بآخر فئتين
        ->pluck('category_id');

    foreach ($recentCategories as $categoryId) {
        $categoryInfo = Category::find($categoryId);
        if ($categoryInfo) {
            $books = AudioBook::where('status', 'approved')
                ->where('category_id', $categoryId)
                ->whereNotIn('id', $listenedBookIds)
                ->inRandomOrder()->limit(7)->get();

            // فقط أضف القسم إذا كان يحتوي على كتب
            if ($books->isNotEmpty()) {
                $recommendations['by_category_' . $categoryId] = [
                    'title' => 'مقترحات من فئة ' . $categoryInfo->name,
                    'books' => $books
                ];
            }
        }
    }

    // --- المحور الثاني: التوصية بناءً على الكتب الأعلى تقييماً (دائماً يظهر) ---
    $mostRatedBooks = AudioBook::where('status', 'approved')
        ->whereNotIn('id', $listenedBookIds)
        ->withCount('ratings')
        ->orderByDesc('ratings_count')
        ->limit(7)->get();

    if ($mostRatedBooks->isNotEmpty()) {
        $recommendations['most_rated'] = [
            'title' => 'الأعلى تقييماً على المنصة',
            'books' => $mostRatedBooks
        ];
    }
    // =================================================================
    // ▲▲▲ نهاية نظام التوصيات الجديد ▲▲▲
    // =================================================================

    // ▼▼▼ التعديل الجديد: إضافة الإحصائيات الجديدة ▼▼▼
    $userStats = [
        'total_ratings' => Rating::where('listener_id', $listener->id)->count(),
        'total_comments' => Comment::where('listener_id', $listener->id)->count(),
        'total_listening_history' => ListeningHistory::where('listener_id', $listener->id)->count(),
    ];
    // ▲▲▲ نهاية الإحصائيات الجديدة ▲▲▲

    // --- 4. Send all data to the view ---
    return view('listener.dashboard', compact(
        'audioBooks', 'playlists', 'unreadNotificationsCount', 'latestNotifications',
        'bookmark', 'recommendations', 'continueListening', 'userStats' // تم إضافة userStats
    ));
}



    /**
     * Log listening history for an audiobook.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
  // لا تنس إضافة هذا السطر في أعلى الملف مع باقي جمل الـ "use"


// ... (بقية الكود)

public function logListeningHistory(Request $request, AchievementService $achievementService): JsonResponse
{
    $request->validate(['audio_book_id' => 'required|exists:audio_books,id']);

    /** @var \App\Models\User $listener */
    $listener = Auth::user();

    ListeningHistory::updateOrCreate(
        ['listener_id' => $listener->id, 'audio_book_id' => $request->audio_book_id],
        ['listened_at' => now()]
    );

    // ▼▼▼ الكود الجديد الذي تمت إضافته ▼▼▼
    // تشغيل محرك الإنجازات للتحقق من وجود إنجازات جديدة
    $newAchievements = $achievementService->checkAndAwardAchievements($listener);

    // إرجاع الإنجازات الجديدة في الرد حتى نتمكن من عرض إشعار بها
    return response()->json([
        'success' => true,
        'new_achievements' => $newAchievements
    ]);
    // ▲▲▲ نهاية الكود الجديد ▲▲▲
}


    /**
     * Show the details page for a single audiobook.
     *
     * @param \App\Models\AudioBook $audioBook
     * @return \Illuminate\View\View
     */
    public function showAudioBook(AudioBook $audioBook): View
    {
       // ▼▼▼ هذا هو الكود الجديد الذي يسمح للادمن بالمرور ▼▼▼
if (Auth::user()->role !== 'admin' && $audioBook->status !== 'approved') {
    abort(404, 'هذا الكتاب غير متاح للعرض حالياً.');
}

        $audioBook->load(['comments.user', 'publisher', 'category']);
        /** @var \App\Models\User $listener */
        $listener = Auth::user();
        $playlists = $listener->playlists;
        $userRating = $audioBook->ratings()->where('listener_id', $listener->id)->first();
        $back_route = route('listener.dashboard');
        return view('listener.audiobook_detail', compact('audioBook', 'playlists', 'userRating', 'back_route'));
    }

    /**
     * Show a publisher's approved audiobooks.
     *
     * @param \App\Models\User $publisher
     * @return \Illuminate\View\View
     */
    public function showPublisherAudioBooks(User $publisher): View
    {
        $audiobooks = $publisher->audioBooks()->where('status', 'approved')->paginate(15);
        return view('listener.publisher_books', compact('publisher', 'audiobooks'));
    }

    /**
     * Show the publishers the user is subscribed to.
     *
     * @return \Illuminate\View\View
     */
    public function subscribedPublishers(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $publishers = $user->subscriptions()->paginate(15);
        return view('listener.subscribed_publishers', compact('publishers'));
    }

    /**
     * Rate an audiobook.
     */
    public function rate(Request $request, AudioBook $audioBook): JsonResponse
    {
        $request->validate(['rating' => 'required|integer|min:1|max:5']);
        Rating::updateOrCreate(
            ['listener_id' => Auth::id(), 'audio_book_id' => $audioBook->id],
            ['rating' => $request->rating]
        );
        return response()->json(['success' => true]);
    }

    /**
     * Remove a rating from an audiobook.
     */
    public function removeRating(AudioBook $audioBook): JsonResponse
    {
        Rating::where('listener_id', Auth::id())
            ->where('audio_book_id', $audioBook->id)
            ->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Handle the download of an audio book.
     */
    public function downloadAudio(AudioBook $audioBook)
    {
        if ($audioBook->status !== 'approved') {
            abort(403, 'This audiobook is not approved for download.');
        }
        $filePath = $audioBook->file_path;
        $disk = Storage::disk('public');
        if (!$disk->exists($filePath)) {
            return back()->with('warning', 'عذراً، الملف الصوتي غير موجود حالياً على الخادم.');
        }
        Download::create(['listener_id' => Auth::id(), 'audio_book_id' => $audioBook->id]);
        if (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: ' . $disk->mimeType($filePath));
        header('Content-Length: ' . $disk->size($filePath));
        header('Content-Disposition: attachment; filename="' . Str::slug($audioBook->title) . '.mp3"');
        header('Pragma: public');
        readfile($disk->path($filePath));
        exit;
    }

    /**
     * Show the page with downloaded audio files.
     */
    public function showDownloadedAudio(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $downloads = $user->downloads()->with('audioBook')->latest()->get();
        return view('listener.downloaded_audio', compact('downloads'));
    }

    /**
     * Show all notifications for the user.
     */
    public function allNotifications(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(20);
        return view('listener.notifications.index', compact('notifications'));
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        return back()->with('success', 'تم تمييز كل الإشعارات كمقروءة.');
    }

    /**
     * Mark a single notification as read.
     */
    public function markOneAsRead($notificationId)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $notificationId )->firstOrFail();
        $notification->markAsRead();
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        if (isset($notification->data['link'])) {
            return redirect($notification->data['link']);
        }
        return back();
    }

    /**
     * Streams the audio file with support for seeking (HTTP Range Requests).
     */
    public function streamAudio(AudioBook $audioBook)
    {
        if ($audioBook->status !== 'approved') {
            abort(403);
        }
        $path = Storage::disk('public')->path($audioBook->file_path);
        if (!file_exists($path)) {
            abort(404);
        }
        return response()->file($path);
    }

    /**
     * Show the audio player page.
     */
    public function playAudio(AudioBook $audioBook): View
    {
        if ($audioBook->status !== 'approved') {
            abort(403);
        }
        $startTime = request('startTime', 0);
        return view('listener.playAudio', compact('audioBook', 'startTime'));
    }
    /**
 * يعرض صفحة إنجازات المستخدم.
 */
public function myAchievements(): View
{
    /** @var \App\Models\User $listener */
    $listener = Auth::user();

    // جلب كل الإنجازات المتاحة في النظام
    $allAchievements = \App\Models\Achievement::all();

    // جلب الإنجازات التي حصل عليها المستخدم مع تاريخ الحصول عليها
    $unlockedAchievements = $listener->achievements()->get()->keyBy('id');

    return view('listener.my_achievements', compact('allAchievements', 'unlockedAchievements'));
}

}
