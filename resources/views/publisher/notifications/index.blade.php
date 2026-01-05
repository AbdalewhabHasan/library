<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>كل الإشعارات - لوحة تحكم الناشر</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100% );
            --shadow-light: 0 8px 32px 0 rgba(31, 38, 135, 0.37);

            /* الوضع النهاري (الافتراضي) */
            --bg-color: #f0f2f5;
            --card-bg: rgba(255, 255, 255, 0.9);
            --card-border: rgba(0, 0, 0, 0.05);
            --text-color: #1a202c;
            --text-muted: #718096;
        }

        html[data-theme='dark'] {
            /* الوضع الليلي */
            --bg-color: #1a202c;
            --card-bg: rgba(26, 32, 44, 0.7);
            --card-border: rgba(255, 255, 255, 0.1);
            --text-color: #edf2f7;
            --text-muted: #a0aec0;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s ease, color 0.3s ease;
            min-height: 100vh;
            padding: 2rem;
        }
        .main-container {
            max-width: 900px;
            margin: auto;
        }
        .notifications-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--card-border);
            border-radius: 25px;
            padding: 2rem 3rem;
            box-shadow: var(--shadow-light);
        }
        .notifications-card h1 {
            font-weight: 700;
            text-align: center;
            margin-bottom: 2.5rem;
            text-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .notification-item {
            display: flex;
            align-items: center;
            padding: 1.25rem;
            border-bottom: 1px solid var(--card-border);
            text-decoration: none;
            color: var(--text-color);
            transition: background-color 0.3s, transform 0.3s;
            border-radius: 15px;
            margin-bottom: 1rem;
        }
        .notification-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .notification-item:hover {
            background-color: rgba(102, 126, 234, 0.1);
            transform: translateX(-10px);
        }
        .notification-item.read {
            opacity: 0.6;
        }
        .notification-item.read:hover {
            opacity: 1;
        }
        .notification-icon {
            font-size: 1.5rem;
            width: 50px;
            text-align: center;
            margin-left: 1rem;
            color: #4facfe;
        }
        .notification-content p {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
        }
        .notification-time {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }
        .pagination .page-link {
            background: transparent !important;
            border: 1px solid var(--card-border) !important;
            color: var(--text-color) !important;
            border-radius: 0.5rem !important;
            margin: 0 5px;
            transition: all 0.3s;
        }
        .pagination .page-item.active .page-link {
            background: var(--primary-gradient) !important;
            border-color: transparent !important;
            color: white !important;
        }
        .pagination .page-item:not(.disabled) .page-link:hover {
            background: rgba(102, 126, 234, 0.1) !important;
        }
        .pagination .page-item.disabled .page-link {
            opacity: 0.5;
        }
        .btn-back {
            display: inline-block;
            margin-top: 2.5rem;
            color: var(--text-color);
            text-decoration: none;
            font-weight: 600;
            background: var(--card-bg);
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            border: 1px solid var(--card-border);
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: rgba(102, 126, 234, 0.2);
            box-shadow: var(--shadow-light);
        }
        .empty-state {
            text-align: center;
            padding: 4rem 0;
            color: var(--text-muted);
        }
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="notifications-card">
            <h1><i class="fas fa-history"></i> سجل الإشعارات</h1>

            @if($notifications->count() > 0)
                @foreach ($notifications as $notification)
                    {{-- ▼▼▼ تم تعديل الرابط هنا ليستخدم المسار الجديد ▼▼▼ --}}
                    <a href="{{ route('publisher.notifications.markOneAsRead', $notification->id) }}" class="notification-item {{ !$notification->is_read ? '' : 'read' }}">
                        <div class="notification-icon">
                            <i class="{{ $notification->icon ?? 'fas fa-info-circle' }}"></i>
                        </div>
                        <div class="notification-content">
                           {{-- ▼▼▼ هذا هو التصحيح الصحيح 100% ▼▼▼ --}}
<p class="notification-message">{{ $notification->data['message'] ?? 'إشعار فارغ' }}</p>

                            <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                        </div>
                    </a>
                @endforeach

                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p class="mt-3 fs-5">لا توجد إشعارات لعرضها حتى الآن.</p>
                </div>
            @endif

            <div class="text-center">
                <a href="{{ route('publisher.dashboard') }}" class="btn-back"><i class="fas fa-arrow-right"></i> العودة للوحة التحكم</a>
            </div>
        </div>
    </div>

    <script>
        // كود بسيط لجعل الوضع الليلي يعمل في هذه الصفحة أيضاً
        document.addEventListener('DOMContentLoaded', function() {
            const currentTheme = localStorage.getItem('theme');
            if (currentTheme) {
                document.documentElement.setAttribute('data-theme', currentTheme);
            }
        });
    </script>
</body>
</html>
