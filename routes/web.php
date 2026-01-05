<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\ListenerController;
use App\Http\Controllers\AudioBookController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\ReportController; // ▼▼▼ تم إضافة هذا السطر ▼▼▼
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AudioBookController as AdminAudioBookController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\SubscriptionController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request; // قد يكون هذا السطر موجوداً بالفعل في الأعلى
use App\Models\AudioBook;   // تأكد من وجود هذا السطر في الأعلى أيضاً

use App\Http\Controllers\VoiceCommandController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- الصفحات العامة والمصادقة ---
Route::get('/', function () {
    return view('welcome');
});
require __DIR__.'/auth.php';

// --- التوجيه بعد تسجيل الدخول ---
Route::get('/home', function () {
    $user = Auth::user();
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'publisher') {
        return redirect()->route('publisher.dashboard');
    } elseif ($user->role === 'listener') {
        return redirect()->route('listener.dashboard');
    }
    return redirect('/profile');
})->middleware('auth')->name('home');

// --- المسارات المشتركة (ملف شخصي، بحث عام) ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/audiobooks/{audioBook}', [ListenerController::class, 'showAudioBook'])->name('listener.audiobook.show');
    Route::get('/audio-books/all', [AudioBookController::class, 'allAudioBooks'])->name('audio-books.all');
    Route::post('/subscribe/{publisher}', [SubscriptionController::class, 'subscribe'])->name('subscribe');
    Route::post('/unsubscribe/{publisher}', [SubscriptionController::class, 'unsubscribe'])->name('unsubscribe');
    Route::post('/voice-command', [VoiceCommandController::class, 'handle'])
     ->middleware('listener') // يكفي إضافة 'listener' هنا لأن 'auth' مطبق على المجموعة كلها
     ->name('voice.command');
 Route::post('/reports/store', [\App\Http\Controllers\ReportController::class, 'store'])->name('listener.reports.store');
});

// =================================================
//           مسارات الناشر (Publisher)
// =================================================
Route::middleware(['auth', 'publisher'])->prefix('publisher')->name('publisher.')->group(function () {
    Route::get('/dashboard', [PublisherController::class, 'index'])->name('dashboard');
    Route::get('/audio-books', [PublisherController::class, 'showAudioBooks'])->name('audio-books.index');
    Route::get('/audio-books/create', [PublisherController::class, 'createAudioBook'])->name('audio-books.create');
    Route::post('/audio-books', [PublisherController::class, 'storeAudioBook'])->name('audio-books.store');
   // ▼▼▼ الأسطر الصحيحة لمجموعة الناشر ▼▼▼
Route::get('/audio-books/{audioBook}/edit', [PublisherController::class, 'editAudioBook'])->name('audio-books.edit');
Route::put('/audio-books/{audioBook}', [PublisherController::class, 'updateAudioBook'])->name('audio-books.update');
Route::delete('/audio-books/{audioBook}', [PublisherController::class, 'destroyAudioBook'])->name('audio-books.destroy');

    Route::get('/notifications', [PublisherController::class, 'allNotifications'])->name('notifications.index');
    Route::post('/notifications/mark-all-as-read', [PublisherController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/notifications/{notification}/read', [PublisherController::class, 'markOneAsRead'])->name('notifications.markOneAsRead');
    // ... داخل Route::middleware(['auth', 'publisher'])->prefix('publisher')->name('publisher.')->group(function () { ...

// مسار لعرض صفحة إدارة التعليقات
Route::get('/comments', [PublisherController::class, 'manageComments'])->name('comments.manage');

// مسار لحذف تعليق معين (سنستخدمه لاحقاً من الصفحة)
Route::delete('/comments/{comment}', [PublisherController::class, 'deleteComment'])->name('comments.delete');

});

// =================================================
//           مسارات المستمع (Listener)
// =================================================
Route::middleware(['auth', 'listener'])->prefix('listener')->name('listener.')->group(function () {
    Route::get('/dashboard', [ListenerController::class, 'index'])->name('dashboard');
    Route::post('/log-listening-history', [ListenerController::class, 'logListeningHistory'])->name('log.history');
    Route::get('/publisher/{publisher}/audio-books', [ListenerController::class, 'showPublisherAudioBooks'])->name('publisher.audioBooks');
    Route::get('/subscribed-publishers', [ListenerController::class, 'subscribedPublishers'])->name('subscribedPublishers');
    Route::get('/playlists', [PlaylistController::class, 'index'])->name('playlists.index');
    Route::get('/playlists/create', [PlaylistController::class, 'create'])->name('playlists.create');
    Route::post('/playlists', [PlaylistController::class, 'store'])->name('playlists.store');
    Route::get('/playlists/{playlist}', [PlaylistController::class, 'show'])->name('playlists.show');
    Route::delete('/playlists/{playlist}', [PlaylistController::class, 'destroy'])->name('playlists.destroy');
    Route::post('/playlist/addAudio', [PlaylistController::class, 'addAudio'])->name('playlist.addAudio');
    Route::delete('/playlists/{playlist}/remove-audio/{audioBook}', [PlaylistController::class, 'removeAudioBook'])->name('playlist.removeAudioBook');
    // ▼▼▼ الكود الصحيح لملف web.php ▼▼▼

// مسار إضافة/تحديث التقييم
Route::post('/rate/{audioBook}', [ListenerController::class, 'rate'])->name('rate');

// مسار حذف التقييم
Route::delete('/audio-books/{audioBook}/remove-rating', [ListenerController::class, 'removeRating'])->name('rating.remove');

    Route::post('/comments/{audioBook}', [CommentController::class, 'addComment'])->name('comments.add');
    Route::get('/comments/{audioBook}', [CommentController::class, 'showComments'])->name('comments.show');
    Route::get('/comments/{comment}/edit', [CommentController::class, 'edit'])->name('comments.edit');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/bookmark', [BookmarkController::class, 'store'])->name('bookmark.store');
  // في ملف routes/web.php

// الكود الصحيح والنهائي
Route::get('/audio-stream/{audioBook}', [ListenerController::class, 'streamAudio'])->name('listener.audio.stream');
Route::get('/playAudio/{audioBook}', [ListenerController::class, 'playAudio'])->name('listener.playAudio');
Route::post('/listener/bookmark/update', [App\Http\Controllers\BookmarkController::class, 'update'])->name('listener.bookmark.update');
// يمكنك وضعه قبل القوس الأخير الخاص بالمجموعة
Route::get('/my-achievements', [ListenerController::class, 'myAchievements'])->name('my.achievements');

    Route::get('/download/{audioBook}', [ListenerController::class, 'downloadAudio'])->name('download');
    Route::get('/downloaded-audio', [ListenerController::class, 'showDownloadedAudio'])->name('downloadedAudio');
    Route::post('/notifications/mark-as-read', [ListenerController::class, 'markAllAsRead']);
    Route::post('/notifications/mark-one-as-read/{notification}', [ListenerController::class, 'markOneAsRead'])->name('notifications.markOneAsRead');
    Route::get('/notifications', [ListenerController::class, 'allNotifications'])->name('notifications.index');
    Route::get('audiobook/{audioBook}', [ListenerController::class, 'showAudioBook'])->name('audiobook.show');

    // ▼▼▼ مسارات الإبلاغات الجديدة ▼▼▼

    // ▲▲▲ نهاية مسارات الإبلاغات ▲▲▲
    // routes/web.php

// ... (بقية المسارات)

// ▼▼▼ أضف هذا السطر ▼▼▼


});

// =================================================
//         مسارات لوحة تحكم الأدمن (Admin)
// =================================================
Route::middleware(['auth', 'is.admin'])->prefix('admin')->name('admin.')->group(function () {
// ... داخل مجموعة مسارات الأدمن

// سطر واحد لإدارة كل ما يخص الإعلانات (عرض، إضافة، حفظ، حذف)
Route::resource('advertisements', \App\Http\Controllers\Admin\AdvertisementController::class);
// ... داخل Route::middleware(['auth', 'is.admin'])->prefix('admin')->name('admin.')->group(function () { ...


    // لوحة التحكم الرئيسية وسجل الإشعارات
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/notifications', [AdminDashboardController::class, 'allNotifications'])->name('notifications.index');
    Route::post('/notifications/mark-all-as-read', [AdminDashboardController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

    // إدارة المستخدمين
    Route::resource('users', UserController::class);
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');

    // إدارة الكتب الصوتية
    Route::get('/audiobooks', [AdminAudioBookController::class, 'index'])->name('audiobooks.index');
   Route::get('/audiobooks/{audioBook}/preview', [AdminAudioBookController::class, 'preview'])->name('audiobooks.preview');
    // ▼▼▼ تم التأكد من أن هذا المسار يستخدم دالة destroy المعدلة ▼▼▼
    Route::delete('/audiobooks/{audioBook}', [AdminAudioBookController::class, 'destroy'])->name('audiobooks.destroy');
    Route::patch('/audiobooks/{audioBook}/approve', [AdminAudioBookController::class, 'approve'])->name('audiobooks.approve');
    Route::patch('/audiobooks/{audioBook}/reject', [AdminAudioBookController::class, 'reject'])->name('audiobooks.reject');
    // ▲▲▲ انتهى التعديل ▲▲▲

    // إدارة الفئات
    Route::resource('categories', CategoryController::class)->except(['show']);

    // إدارة خطط الاشتراك
    Route::resource('plans', PlanController::class);

    // إدارة الإعدادات
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'store'])->name('settings.store');

    // ▼▼▼ مسارات الإبلاغات للمشرف (جديدة) ▼▼▼
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::patch('/reports/{report}/status', [ReportController::class, 'updateStatus'])->name('reports.updateStatus');
    Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');
    // ▲▲▲ نهاية مسارات الإبلاغات ▲▲▲
});

// =================================================
//           مسارات الدفع والاشتراكات
// =================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/subscribe', [PaymentController::class, 'showSubscriptionPage'])->name('subscribe.page');
    Route::post('/subscribe/checkout', [PaymentController::class, 'processSubscription'])->name('subscribe.process');
    Route::get('/subscribe/success', [PaymentController::class, 'subscriptionSuccess'])->name('subscribe.success');
    Route::get('/subscribe/cancel', [PaymentController::class, 'subscriptionCancelled'])->name('subscribe.cancel');
});

// =================================================
//           مسارات Webhook (عامة وبدون حماية CSRF)
// =================================================
Route::post('/stripe/webhook', [PaymentController::class, 'handleWebhook'])->name('stripe.webhook');

// =================================================
//           مسار طوارئ لإنشاء مستخدم
// =================================================
Route::get('/create-emergency-user', function () {
    User::where('email', 'listener@gmail.com')->delete();
    $user = User::create([
        'name' => 'Listener User',
        'email' => 'listener@gmail.com',
        'role' => 'listener',
        'password' => Hash::make('password'),
    ]);
    return "<h1>تم إنشاء المستخدم بنجاح!</h1>
            <p>اذهب الآن إلى صفحة تسجيل الدخول وجرب:</p>
            <p><strong>الإيميل:</strong> listener@gmail.com</p>
            <p><strong>كلمة المرور:</strong> password</p>";
});
// هذا المسار سيستقبل الأوامر الصوتية

// في نهاية ملف routes/web.php

 // تأكد من وجود هذا السطر في أعلى الملف

Route::get('/api/audiobook/{audioBook}/average-rating', function (AudioBook $audioBook) {
    return response()->json([
        'average_rating' => $audioBook->ratings()->avg('rating') ?? 0
    ]);
})->name('api.audiobook.rating'); // أضفنا اسماً للراوت ليكون أفضل
// =================================================
//   API Route for Search Suggestions
// =================================================

Route::get('/api/search-suggestions', function (Request $request) {
    // استلام كلمة البحث من الطلب
    $query = $request->get('query', '');

    // لا تقم بالبحث إذا كانت الكلمة قصيرة جداً لتخفيف الضغط على السيرفر
    if (strlen($query) < 2) {
        return response()->json([]);
    }

    // البحث في جدول الكتب عن العناوين المتطابقة
    $books = AudioBook::where('status', 'approved')
                      ->where('title', 'LIKE', "%{$query}%")
                      ->select('id', 'title') // نختار فقط الحقول التي نحتاجها
                      ->limit(5) // نكتفي بـ 5 اقتراحات
                      ->get();

    // إرجاع النتائج كـ JSON
    return response()->json($books);
})->name('api.search.suggestions');
