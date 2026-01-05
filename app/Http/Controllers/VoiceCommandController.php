<?php

namespace App\Http\Controllers;

use App\Models\AudioBook;
use App\Models\Playlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Comment;   // <-- إضافة جديدة
use App\Models\Bookmark;  // <-- إضافة جديدة

class VoiceCommandController extends Controller
{
    public function handle(Request $request)
    {
        // التحقق من الأوامر المعقدة أولاً (التي تأتي ككائن JSON)
        if ($request->has('action')) {
            $action = $request->input('action');
            switch ($action) {
                case 'addToPlaylist':
                    return $this->handleAddToPlaylist($request);
                case 'download':
                    return $this->handleDownload($request);

                // ▼▼▼ الأوامر الجديدة التي تمت إضافتها ▼▼▼
                case 'addComment':
                    return $this->handleAddComment($request);
                case 'addBookmark':
                    return $this->handleAddBookmark($request);
                // ▲▲▲ انتهت الإضافة ▲▲▲
            }
        }

        // إذا لم يكن أمراً معقداً، تعامل معه كأمر تنقل بسيط
        return $this->handleNavigation($request);
    }

    /**
     * يعالج أوامر التنقل بين الصفحات (مثل "اذهب إلى صفحة ...")
     * (هذه الدالة من كودك الأصلي ولم يتم تغييرها)
     */
    private function handleNavigation(Request $request)
    {
        $command = $request->input('command');
        $prefix = 'اذهب إلى صفحة ';

        if (!Str::startsWith($command, $prefix)) {
            if (Str::contains($command, ['تسجيل الخروج', 'اخرج'])) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return response()->json(['action' => 'redirect', 'url' => route('login')]);
            }
            return response()->json(['action' => 'error', 'message' => 'الأمر يجب أن يبدأ بـ "اذهب إلى صفحة ..."']);
        }

        $keyword = Str::after($command, $prefix);
        $keyword = trim($keyword, " .");

        $navigationKeywords = [
            'الرئيسية'           => 'listener.dashboard',
            'لوحة التحكم'        => 'listener.dashboard',
            'اشتراكاتي'          => 'listener.subscribedPublishers',
            'الناشرون'            => 'listener.subscribedPublishers',
            'قوائم التشغيل'       => 'listener.playlists.index',
            'قوائمي'             => 'listener.playlists.index',
            'إنشاء قائمة تشغيل'  => 'listener.playlists.create',
            'قائمة جديدة'         => 'listener.playlists.create',
            'تحميلاتي'           => 'listener.downloadedAudio',
            'الملفات المحملة'    => 'listener.downloadedAudio',
            'الإشعارات'           => 'listener.notifications.index',
            'التنبيهات'           => 'listener.notifications.index',
            'كل الكتب'           => 'audio-books.all',
            'المكتبة'            => 'audio-books.all',
            'ملفي الشخصي'        => 'profile.edit',
            'حسابي'              => 'profile.edit',
            'الاشتراك'            => 'subscribe.page',
            'الترقية'            => 'subscribe.page',
            'الخطط'               => 'subscribe.page',
        ];

        if (isset($navigationKeywords[$keyword])) {
            $routeName = $navigationKeywords[$keyword];
            return response()->json(['action' => 'redirect', 'url' => route($routeName)]);
        }

        return response()->json(['action' => 'error', 'message' => 'لم أتعرف على الصفحة: ' . $keyword]);
    }

    /**
     * يعالج أمر "إضافة إلى قائمة التشغيل"
     * (هذه الدالة من كودك الأصلي ولم يتم تغييرها)
     */
   /**
 * يعالج أمر "إضافة إلى قائمة التشغيل"
 */
private function handleAddToPlaylist(Request $request)
{
    $playlistName = $request->input('playlistName');
    $audioBookId = $request->input('audioBookId');

    // ▼▼▼ هذا هو السطر الذي سيحل المشكلة ▼▼▼
    /** @var \App\Models\User $user */
    $user = Auth::user();
    // ▲▲▲ انتهى التعديل ▲▲▲

    $playlist = $user->playlists()->where('name', 'like', '%' . $playlistName . '%')->first();

    if (!$playlist) {
        return response()->json(['action' => 'feedback', 'message' => "لم أجد قائمة تشغيل باسم '{$playlistName}'"]);
    }
    $audioBook = AudioBook::find($audioBookId);
    if (!$audioBook) {
        return response()->json(['action' => 'feedback', 'message' => "خطأ: الكتاب الصوتي غير موجود."]);
    }
    if ($playlist->audioBooks()->where('audio_book_id', $audioBookId)->exists()) {
        return response()->json(['action' => 'feedback', 'message' => "الكتاب موجود بالفعل في قائمة '{$playlist->name}'"]);
    }
    $playlist->audioBooks()->attach($audioBookId);
    return response()->json(['action' => 'feedback', 'message' => "تمت إضافة الكتاب إلى قائمة '{$playlist->name}' بنجاح!"]);
}

    /**
     * يعالج أمر "تحميل الكتاب"
     * (هذه الدالة من كودك الأصلي ولم يتم تغييرها)
     */
    private function handleDownload(Request $request)
    {
        $audioBookId = $request->input('audioBookId');
        $audioBook = AudioBook::find($audioBookId);

        if (!$audioBook || !Storage::disk('public')->exists($audioBook->file_path)) {
            return response()->json(['action' => 'error', 'message' => 'الملف غير موجود.']);
        }
        return response()->json(['action' => 'download_success', 'download_url' => route('listener.download', $audioBook)]);
    }

    // ▼▼▼ الدوال الجديدة التي تمت إضافتها ▼▼▼

    /**
     * يعالج أمر "إضافة تعليق"
     */
    private function handleAddComment(Request $request)
    {
        $audioBookId = $request->input('audioBookId');
        $commentText = $request->input('comment');

        if (!$audioBookId || !$commentText) {
            return response()->json(['action' => 'error', 'message' => 'معلومات التعليق ناقصة.']);
        }
        if (!AudioBook::find($audioBookId)) {
            return response()->json(['action' => 'error', 'message' => 'الكتاب الصوتي غير موجود.']);
        }

        Comment::create([
            'listener_id' => Auth::id(),
            'audio_book_id' => $audioBookId,
            'comment' => $commentText,
        ]);

        return response()->json(['action' => 'feedback', 'message' => "تمت إضافة تعليقك بنجاح!"]);
    }

    /**
     * يعالج أمر "إضافة إشارة مرجعية"
     */
    private function handleAddBookmark(Request $request)
    {
        $audioBookId = $request->input('audioBookId');
        $time = $request->input('time');

        if (!$audioBookId) {
            return response()->json(['action' => 'error', 'message' => 'معلومات الإشارة المرجعية ناقصة.']);
        }
        if (!AudioBook::find($audioBookId)) {
            return response()->json(['action' => 'error', 'message' => 'الكتاب الصوتي غير موجود.']);
        }

        Bookmark::updateOrCreate(
            ['listener_id' => Auth::id(), 'audio_book_id' => $audioBookId],
            ['time' => $time]
        );

        $minutes = floor($time / 60);
        $seconds = $time % 60;

        return response()->json(['action' => 'feedback', 'message' => "تم حفظ الإشارة المرجعية عند الدقيقة {$minutes} والثانية {$seconds}"]);
    }
    // ▲▲▲ انتهت الإضافة ▲▲▲
}
