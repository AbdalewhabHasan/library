{{-- ====================================================================== --}}
{{-- ==   ملف الإشعارات (النسخة النهائية الكاملة 100%)                   == --}}
{{-- ====================================================================== --}}

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كل الإشعارات</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token( ) }}">
    <style>
        :root {
            --primary-color: #667eea; --primary-dark: #5a67d8; --secondary-color: #764ba2;
            --dark-color: #2d3748; --light-color: #f7fafc; --white: #ffffff;
            --gray-100: #f7fafc; --gray-200: #edf2f7; --gray-300: #e2e8f0; --gray-500: #a0aec0; --gray-700: #4a5568;
            --success-color: #48bb78; --success-dark: #38a169;
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --radius-xl: 1rem; --radius-lg: 0.75rem; --radius-md: 0.5rem;
            --transition-normal: 0.3s ease-in-out;
        }
        body {
            font-family: 'Cairo', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh; color: var(--dark-color); line-height: 1.6; padding: 2rem; margin: 0;
        }
        .container {
            max-width: 900px; margin: 0 auto; background: rgba(255, 255, 255, 0.98);
            border-radius: var(--radius-xl); padding: 2rem 3rem; box-shadow: var(--shadow-lg);
        }
        .page-header {
            display: flex; justify-content: space-between; align-items: center; text-align: center;
            margin-bottom: 2.5rem; border-bottom: 1px solid var(--gray-200); padding-bottom: 1.5rem;
        }
        .page-header h1 { font-size: 2.2rem; font-weight: 700; color: var(--dark-color); flex-grow: 1; margin: 0; }
        .notifications-list { list-style: none; padding: 0; margin: 0; }
        .notification-list-item {
            display: flex; align-items: center; gap: 1.5rem; padding: 1.5rem; border-bottom: 1px solid var(--gray-200);
            text-decoration: none; color: var(--gray-700); transition: background-color var(--transition-normal);
        }
        .notification-list-item:last-child { border-bottom: none; }
        .notification-list-item:hover { background-color: var(--gray-100); }
        .notification-list-item.unread { background-color: #ebf8ff; font-weight: 600; }
        .notification-icon { font-size: 1.5rem; color: var(--primary-color); width: 40px; text-align: center; }
        .notification-content { flex-grow: 1; }
        .notification-message { margin: 0; font-size: 1rem; }
        .notification-time { font-size: 0.8rem; color: var(--gray-500); margin-top: 0.25rem; }
        .pagination-container { margin-top: 2rem; display: flex; justify-content: center; }
        .pagination-container .pagination { display: flex; list-style: none; padding: 0; gap: 0.5rem; }
        .pagination-container .page-item .page-link { padding: 0.5rem 1rem; border-radius: var(--radius-md); text-decoration: none; color: var(--primary-color); background-color: white; border: 1px solid var(--gray-300); transition: all var(--transition-normal); }
        .pagination-container .page-item.active .page-link { background-color: var(--primary-color); color: white; border-color: var(--primary-color); }
        .pagination-container .page-item:not(.disabled) .page-link:hover { background-color: var(--gray-100); }
        .btn { display: inline-block; text-decoration: none; padding: 0.75rem 1.5rem; color: white; border-radius: var(--radius-lg); font-weight: 600; transition: background-color var(--transition-normal); border: none; cursor: pointer; }
        .btn-back { background: var(--primary-color); }
        .btn-back:hover { background: var(--primary-dark); }
        .btn-mark-all { background: var(--success-color); font-size: 0.9rem; padding: 0.5rem 1rem; }
        .btn-mark-all:hover { background: var(--success-dark); }
        .empty-state { text-align: center; padding: 4rem 2rem; color: var(--gray-500); }
        .empty-state i { font-size: 3rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-history"></i> سجل الإشعارات</h1>
            @if($notifications->where('read_at', null)->count() > 0)
                <form action="{{ route('listener.notifications.markAllAsRead') }}" method="POST" style="margin: 0;">
                    @csrf
                    {{-- ▼▼▼ إضافة ID للزر ▼▼▼ --}}
                    <button type="submit" class="btn btn-mark-all" id="mark-all-read-btn">
                        <i class="fas fa-check-double"></i> تمييز الكل كمقروء
                    </button>
                </form>
            @endif
        </div>

        <ul class="notifications-list">
            @forelse ($notifications as $notification)
                {{-- ▼▼▼ إضافة ID فريد لكل إشعار ▼▼▼ --}}
                <a href="{{ $notification->data['link'] ?? '#' }}" class="notification-list-item {{ !$notification->read_at ? 'unread' : '' }}" id="notification-{{ $notification->id }}">
                    <div class="notification-icon">
                        <i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }}"></i>
                    </div>
                    <div class="notification-content">
                        <p class="notification-message">{{ $notification->data['message'] }}</p>
                        <p class="notification-time">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                </a>
            @empty
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p>لا توجد إشعارات لعرضها.</p>
                </div>
            @endforelse
        </ul>

        <div class="pagination-container">
            {{ $notifications->links() }}
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="{{ route('listener.dashboard') }}" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> العودة للوحة التحكم
            </a>
        </div>
    </div>

    {{-- ▼▼▼ إضافة زر الميكروفون وحاوية الرسائل ▼▼▼ --}}
    <button id="voice-command-btn" style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; border-radius: 50%; background: #ffc107; color: #333; border: none; font-size: 24px; cursor: pointer; z-index: 9999;">🎤</button>
    <div id="voice-feedback" style="position: fixed; bottom: 100px; right: 30px; background-color: rgba(0,0,0,0.8); color: white; padding: 10px 15px; border-radius: 8px; display: none; z-index: 10000;"></div>
</body>
</html>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const voiceBtn = document.getElementById('voice-command-btn');
    const voiceFeedback = document.getElementById('voice-feedback');

    if (!('SpeechRecognition' in window || 'webkitSpeechRecognition' in window)) {
        if(voiceBtn) voiceBtn.style.display = 'none';
        return;
    }

    const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
    recognition.lang = 'ar-SA';
    recognition.interimResults = false;

    voiceBtn.addEventListener('click', () => {
        showVoiceFeedback('استمع الآن...');
        try { recognition.start(); } catch(e) { /* ignore */ }
    });

    recognition.onresult = (event) => {
        const command = event.results[0][0].transcript.trim();
        showVoiceFeedback(`سمعتك تقول: "${command}"`);
        handleNotificationCommand(command);
    };

    recognition.onerror = (event) => showVoiceFeedback(`خطأ: ${event.error}`);

    function handleNotificationCommand(command) {
        const commandLower = command.toLowerCase();

        // --- 1. أمر تمييز الكل كمقروء ---
        if (commandLower.includes("تمييز الكل كمقروء") || commandLower.includes("اقرأ الكل")) {
            const markAllBtn = document.getElementById('mark-all-read-btn');
            if (markAllBtn) {
                showVoiceFeedback('جاري تمييز كل الإشعارات كمقروءة...');
                markAllBtn.click();
            } else {
                showVoiceFeedback('لا توجد إشعارات غير مقروءة لتمييزها.');
            }
            return;
        }

        // --- 2. أمر فتح إشعار معين ---
        if (commandLower.startsWith("افتح إشعار")) {
            const notificationText = command.substring("افتح إشعار".length).trim();
            const targetNotification = findNotificationByText(notificationText);
            if (targetNotification) {
                showVoiceFeedback(`جاري فتح الإشعار...`);
                window.location.href = targetNotification.href;
            } else {
                showVoiceFeedback(`لم أجد إشعاراً يحتوي على "${notificationText}"`);
            }
            return;
        }

        // --- 3. أوامر التنقل العامة ---
        if (handleNavigationCommand(command)) return;

        showVoiceFeedback('أمر غير معروف. جرب "تمييز الكل كمقروء" أو "افتح إشعار..."');
    }

    function findNotificationByText(text) {
        const allNotifications = document.querySelectorAll('.notification-list-item');
        for (let item of allNotifications) {
            const messageElement = item.querySelector('.notification-message');
            if (messageElement && messageElement.textContent.trim().toLowerCase().includes(text.toLowerCase())) {
                return item;
            }
        }
        return null;
    }

    // انسخ دالة handleNavigationCommand من أي ملف آخر والصقها هنا
    function handleNavigationCommand(command) {
        const commandLower = command.toLowerCase();
        const routes = {
            'لوحة التحكم': 'listener.dashboard',
            'قوائم التشغيل': 'listener.playlists.index',
        };
        for (const keyword in routes) {
            if (commandLower.includes(keyword)) {
                const routeName = routes[keyword];
                showVoiceFeedback(`جاري الانتقال إلى صفحة ${keyword}...`);
                const baseUrl = "{{ url('/') }}";
                let path = routeName.replace(/\./g, '/');
                window.location.href = `${baseUrl}/${path}`;
                return true;
            }
        }
        return false;
    }

    function showVoiceFeedback(message) {
        voiceFeedback.textContent = message;
        voiceFeedback.style.display = 'block';
        setTimeout(() => { voiceFeedback.style.display = 'none'; }, 5000);
    }
});
</script>
@endpush
