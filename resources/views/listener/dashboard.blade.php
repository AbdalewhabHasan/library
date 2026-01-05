<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم المستمع</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token( ) }}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* CSS الأصلي الذي أرسلته بدون أي تعديلات جمالية إضافية */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap' );
        :root {
            --primary-color: #667eea; --primary-dark: #5a67d8; --secondary-color: #764ba2; --accent-color: #f093fb;
            --success-color: #48bb78; --warning-color: #ed8936; --danger-color: #f56565; --info-color: #4299e1;
            --dark-color: #2d3748; --light-color: #f7fafc; --white: #ffffff; --gray-100: #f7fafc; --gray-200: #edf2f7;
            --gray-300: #e2e8f0; --gray-400: #cbd5e0; --gray-500: #a0aec0; --gray-600: #718096; --gray-700: #4a5568;
            --gray-800: #2d3748; --gray-900: #1a202c; --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --radius-sm: 0.375rem; --radius-md: 0.5rem; --radius-lg: 0.75rem; --radius-xl: 1rem;
            --transition-fast: 0.15s ease-in-out; --transition-normal: 0.3s ease-in-out; --transition-slow: 0.5s ease-in-out;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; color: var(--gray-800); line-height: 1.6; }
        nav { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255, 255, 255, 0.2); padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 1000; box-shadow: var(--shadow-md); }
        nav > div:first-child { font-family: 'Poppins', sans-serif; font-size: 1.5rem; font-weight: 700; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .nav-actions { display: flex; align-items: center; gap: 1.5rem; }
        .nav-actions form button { background: linear-gradient(135deg, var(--danger-color), #e53e3e); color: white; border: none; padding: 0.5rem 1.5rem; border-radius: var(--radius-lg); font-weight: 500; cursor: pointer; transition: all var(--transition-normal); box-shadow: var(--shadow-sm); }
        .nav-actions form button:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }
        .notifications-trigger { position: relative; }
        .notification-bell { font-size: 1.5rem; color: var(--gray-600); cursor: pointer; transition: color var(--transition-normal); position: relative; }
        .notification-bell:hover { color: var(--primary-color); }
        .notification-count { position: absolute; top: -5px; right: -10px; background-color: var(--danger-color); color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 0.75rem; font-weight: 700; display: flex; align-items: center; justify-content: center; border: 2px solid white; }
        .container { max-width: 1400px; margin: 0 auto; padding: 2rem; }
        .alert { padding: 1rem 1.5rem; border-radius: var(--radius-lg); margin-bottom: 2rem; font-weight: 500; box-shadow: var(--shadow-md); animation: slideInDown 0.5s ease-out; }
        .alert-success { background: linear-gradient(135deg, #48bb78, #38a169); color: white; border-left: 4px solid #2f855a; }
        .alert-warning { background: linear-gradient(135deg, #ed8936, #dd6b20); color: white; border-left: 4px solid #c05621; }
        .mb-4 { margin-bottom: 2rem; }

        /* ▼▼▼ هذا هو التعديل الوحيد في CSS لضمان التوزيع الصحيح ▼▼▼ */
        .books-grid-row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }
        .books-grid-row > .col {
            padding-right: 15px;
            padding-left: 15px;
            margin-bottom: 2rem; /* يضيف مسافة سفلية للبطاقات */
        }
        /* ▲▲▲ نهاية التعديل في CSS ▲▲▲ */

        .row { display: flex; flex-wrap: wrap; gap: 1rem; align-items: end; }
        .col-md-4, .col-md-6, .col-md-2 { flex: 1; min-width: 200px; }
        .form-label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: white; text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1); }
        .form-select, .form-control { width: 100%; padding: 0.75rem 1rem; border: 2px solid rgba(255, 255, 255, 0.2); border-radius: var(--radius-lg); background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); font-size: 1rem; transition: all var(--transition-normal); box-shadow: var(--shadow-sm); }
        .form-select:focus, .form-control:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); transform: translateY(-1px); }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.75rem 1.5rem; border: none; border-radius: var(--radius-lg); font-weight: 600; text-decoration: none; cursor: pointer; transition: all var(--transition-normal); box-shadow: var(--shadow-md); font-size: 0.95rem; gap: 0.5rem; }
        .btn:hover { transform: translateY(-2px); box-shadow: var(--shadow-xl); }
        .btn-primary { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: white; }
        .btn-success { background: linear-gradient(135deg, var(--success-color), #38a169); color: white; }
        .btn-info { background: linear-gradient(135deg, var(--info-color), #3182ce); color: white; }
        .btn-warning { background: linear-gradient(135deg, var(--warning-color), #dd6b20); color: white; }
        .btn-danger { background: linear-gradient(135deg, var(--danger-color), #e53e3e); color: white; }
        .btn-show-downloaded { background: linear-gradient(135deg, #805ad5, #6b46c1); color: white; }
        .btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none !important; }
        .btn-sm { padding: 0.5rem 1rem; font-size: 0.875rem; }
        .w-100 { width: 100%; }
        .top-buttons { display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 3rem; justify-content: center; }
        .top-buttons .btn { flex: 1; min-width: 200px; max-width: 250px; }
        .card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: var(--radius-xl); overflow: hidden; transition: all var(--transition-normal); box-shadow: var(--shadow-lg); border: 1px solid rgba(255, 255, 255, 0.2); }
        .card:hover { transform: translateY(-8px) scale(1.02); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        .card-img-top { width: 100%; height: 250px; object-fit: cover; transition: transform var(--transition-slow); }
        .card:hover .card-img-top { transform: scale(1.05); }
        .card-body { padding: 1.5rem; }
        .card-title { font-family: 'Poppins', sans-serif; font-size: 1.25rem; font-weight: 600; color: var(--gray-800); margin-bottom: 0.75rem; line-height: 1.4; }
        .card-text { color: var(--gray-600); margin-bottom: 1rem; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .text-muted { color: var(--gray-500) !important; }
        .text-truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .badge { display: inline-block; padding: 0.375rem 0.75rem; font-size: 0.75rem; font-weight: 600; border-radius: var(--radius-lg); text-transform: uppercase; letter-spacing: 0.5px; }
        .bg-info { background: linear-gradient(135deg, var(--info-color), #3182ce); color: white; }
        .bg-secondary { background: linear-gradient(135deg, var(--gray-500), var(--gray-600)); color: white; }
        .d-flex { display: flex; }
        .justify-content-between { justify-content: space-between; }
        .align-items-center { align-items: center; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mt-3 { margin-top: 1rem; }
        .d-block { display: block; }
        .rating-stars { margin-bottom: 0.5rem; }
        .rating-stars .fa-star { font-size: 1.2rem; margin-right: 0.25rem; transition: all var(--transition-fast); }
        .rating-stars .fa-star:hover { transform: scale(1.2); }
        .text-warning { color: #f6ad55 !important; }
        .card-footer { background: rgba(247, 250, 252, 0.8); border-top: 1px solid rgba(226, 232, 240, 0.5); padding: 1rem 1.5rem; display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; }
        .form-group { margin-bottom: 1rem; }
        .d-inline-block { display: inline-block; }
        #audioPlayerContainer { position: fixed; bottom: 0; left: 0; right: 0; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); padding: 1rem 2rem; box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1); border-top: 1px solid rgba(255, 255, 255, 0.2); display: none; z-index: 1000; }
        #audioPlayer { width: 100%; max-width: 600px; margin: 0 auto; display: block; }
        #bookmarkButton { margin-top: 1rem; margin-left: auto; margin-right: auto; display: block; }
        .card-image-container { position: relative; overflow: hidden; }
        .card-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.7); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity var(--transition-normal); }
        .card:hover .card-overlay { opacity: 1; }
        .btn-play { width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: white; border: none; font-size: 1.5rem; cursor: pointer; transition: all var(--transition-normal); box-shadow: var(--shadow-lg); }
        .btn-play:hover { transform: scale(1.1); box-shadow: var(--shadow-xl); }
        .card-meta { margin: 1rem 0; }
        .author-info { margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--gray-200); }
        .author-info small { margin-bottom: 0.25rem; }
        .rating-container { display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; background: rgba(247, 250, 252, 0.5); border-radius: var(--radius-md); margin: 1rem 0; }
        .rating-average { font-weight: 600; }
        .playlist-form { display: flex; flex-direction: column; gap: 0.5rem; min-width: 200px; }
        .comment-section { border-top: 1px solid var(--gray-200); padding-top: 1rem; margin-top: 1rem; }
        .loading-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.7); display: none; align-items: center; justify-content: center; z-index: 9999; }
        .loading-spinner { background: white; padding: 2rem; border-radius: var(--radius-xl); text-align: center; box-shadow: var(--shadow-xl); }
        .notification { position: fixed; top: 20px; right: 20px; padding: 1rem 1.5rem; border-radius: var(--radius-lg); color: white; font-weight: 600; box-shadow: var(--shadow-xl); transform: translateX(100%); transition: transform var(--transition-normal); z-index: 10000; }
        .notification.show { transform: translateX(0); }
        .notification-success { background: linear-gradient(135deg, var(--success-color), #38a169); }
        .notification-error { background: linear-gradient(135deg, var(--danger-color), #e53e3e); }
        .notification-bar { position: fixed; top: -100%; left: 0; width: 100%; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px); box-shadow: var(--shadow-lg); z-index: 1001; transition: top 0.4s ease-in-out; border-bottom: 1px solid var(--gray-300); }
        .notification-bar.show { top: 0; }
        .notification-bar-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid var(--gray-200); }
        .notification-bar-header h3 { margin: 0; font-family: 'Poppins', sans-serif; font-size: 1.2rem; color: var(--dark-color); }
        .notification-bar-header button { background: none; border: none; font-size: 2rem; color: var(--gray-500); cursor: pointer; line-height: 1; }
        .notification-bar-body { max-height: 250px; overflow-y: auto; }
        .notification-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid var(--gray-200); cursor: pointer; transition: background-color var(--transition-fast); }
        .notification-item:last-child { border-bottom: none; }
        .notification-item:hover { background-color: var(--gray-100); }
        .notification-item.unread { background-color: #ebf8ff; }
        .notification-item-message { color: var(--gray-800); font-weight: 500; }
        .notification-item-meta { display: flex; align-items: center; gap: 1rem; color: var(--gray-500); font-size: 0.8rem; }
        .notification-item-read-icon { color: var(--success-color); opacity: 0; transition: opacity var(--transition-normal); }
        .notification-item:not(.unread) .notification-item-read-icon { opacity: 1; }
        .no-notifications-bar { padding: 2rem; text-align: center; color: var(--gray-500); }
        .notification-bar-footer { padding: 0.75rem 1rem; text-align: center; border-top: 1px solid var(--gray-200); background-color: var(--gray-100); }
        .view-all-btn { text-decoration: none; color: var(--primary-color); font-weight: 600; transition: color var(--transition-normal); }
        .view-all-btn:hover { color: var(--primary-dark); }
        .btn-upgrade { display: inline-flex; align-items: center; gap: 0.75rem; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 0.6rem 1.5rem; border-radius: 50px; font-weight: 700; text-decoration: none; box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4); transition: all 0.3s ease; border: 2px solid transparent; }
        .btn-upgrade:hover { transform: translateY(-3px) scale(1.05); box-shadow: 0 8px 25px rgba(245, 87, 108, 0.5); }
        .btn-upgrade i { font-size: 1.2rem; animation: sparkle 1.5s infinite; }
        @keyframes sparkle { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.3); opacity: 0.7; } }
        .section-divider { margin: 3rem 0; border: 0; height: 2px; background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(102, 126, 234, 0.3), rgba(0, 0, 0, 0)); }
        .section-title { font-family: 'Poppins', sans-serif; font-size: 2.2rem; font-weight: 700; color: white; text-shadow: 0 2px 4px rgba(0,0,0,0.2); text-align: center; margin-bottom: 2.5rem; position: relative; }
        .section-title::after { content: ''; display: block; width: 100px; height: 4px; background: linear-gradient(90deg, var(--accent-color), var(--primary-color)); margin: 0.75rem auto 0; border-radius: 2px; }
        /* في نهاية قسم <style> */

/* --- تنسيقات قسم "أكمل الاستماع" --- */
.continue-listening-section { margin-bottom: 4rem; }
.continue-listening-carousel { display: flex; gap: 1.5rem; overflow-x: auto; padding: 1rem; scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; }
.continue-listening-carousel::-webkit-scrollbar { height: 8px; }
.continue-listening-carousel::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 4px; }
.continue-card { background: rgba(0,0,0,0.2); border-radius: var(--radius-md); overflow: hidden; display: flex; align-items: center; width: 320px; flex-shrink: 0; scroll-snap-align: start; transition: var(--transition-normal); }
.continue-card:hover { transform: scale(1.05); }
.continue-card-img { width: 80px; height: 80px; object-fit: cover; }
.continue-card-body { padding: 1rem; flex-grow: 1; }
.continue-card-title { font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.progress-bar-container { background: rgba(255,255,255,0.2); border-radius: 50px; height: 8px; overflow: hidden; }
.progress-bar { background: var(--accent-color); height: 100%; border-radius: 50px; transition: width 0.5s ease; }
.continue-card a { text-decoration: none; }

/* --- تنسيقات البحث الذكي --- */
.search-container { position: relative; }
#search-suggestions { position: absolute; top: 100%; left: 0; right: 0; background: var(--white); border-radius: 0 0 var(--radius-md) var(--radius-md); box-shadow: var(--shadow-lg); z-index: 1000; max-height: 300px; overflow-y: auto; border-top: 1px solid var(--gray-200); display: none; /* مهم: إخفاؤه افتراضياً */ }
.suggestion-item { padding: 0.75rem 1.5rem; cursor: pointer; transition: background-color 0.2s; }
.suggestion-item:hover { background-color: var(--gray-100); }
.suggestion-item a { text-decoration: none; color: var(--gray-800); font-weight: 500; display: block; }
/* ▼▼▼ 2. أضف هذه التنسيقات في نهاية قسم <style> ▼▼▼ */
.card-content-wrapper, .report-interface-wrapper {
    transition: opacity 0.3s ease;
}
.report-interface-wrapper {
    padding: 1rem;
    display: flex;
    flex-direction: column;
    height: 100%;
}
.report-interface-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--gray-200);
}
.report-interface-header h5 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
}
.back-to-card-btn {
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    color: var(--gray-600);
}
.report-interface-list {
    list-style: none;
    padding: 0;
    margin: 0;
    flex-grow: 1;
}
.report-interface-item {
    padding: 0.8rem 0.5rem;
    cursor: pointer;
    font-weight: 500;
    border-radius: var(--radius-sm);
    transition: background-color 0.2s ease;
}
.report-interface-item:hover {
    background-color: var(--gray-200);
}

    </style>
</head>
<body>
{{-- ====================================================================== --}}
{{-- ==   ▼▼▼ 1. النافذة المنبثقة للإبلاغ (خاصة بالداشبورد) ▼▼▼         == --}}
{{-- ====================================================================== --}}

   {{-- ====================================================================== --}}
{{-- ==   هذا هو الكود الكامل لملف dashboard.blade.php مع تعديل الزر فقط  == --}}
{{-- ====================================================================== --}}
<nav>
    <div>
        @if(isset($settings) && $settings->get('site_logo'))
            <img src="{{ asset('storage/' . $settings->get('site_logo')) }}" alt="{{ $settings->get('site_name', 'Logo') }}" style="height: 40px; margin-right: 10px; border-radius: 5px;">
        @else
            <i class="fas fa-headphones"></i>
        @endif
        {{ isset($settings) ? $settings->get('site_name', 'لوحة تحكم المستمع') : 'لوحة تحكم المستمع' }}
    </div>
    <div class="nav-actions">
        <a href="{{ route('subscribe.page') }}" class="btn-upgrade">
            <i class="fas fa-gem"></i>
            <span>الترقية للاشتراك المميز</span>
        </a>
        <div class="notifications-trigger">
            <i class="fas fa-bell notification-bell" id="notificationBell">
                @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                    <span class="notification-count" id="notificationCount">{{ $unreadNotificationsCount }}</span>
                @endif
            </i>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">
                <i class="fas fa-sign-out-alt"></i>
                تسجيل الخروج
            </button>
        </form>
    </div>
</nav>
<div class="notification-bar" id="notificationBar">
    <div class="notification-bar-header">
        <h3>الإشعارات</h3>
        <button id="closeNotificationBar">&times;</button>
    </div>
    <div class="notification-bar-body" id="notificationList">
        @if(isset($latestNotifications))
            @forelse($latestNotifications as $notification)
                <a href="{{ $notification->data['link'] ?? '#' }}" class="notification-item {{ $notification->read_at ? '' : 'unread' }}" data-id="{{ $notification->id }}">
                    <div class="notification-item-main">
                        <i class="fas fa-book-open notification-item-icon"></i>
                        <span class="notification-item-message">{{ $notification->data['message'] ?? 'إشعار غير معروف' }}</span>
                    </div>
                    <div class="notification-item-meta">
                        <span class="notification-item-time">{{ $notification->created_at->diffForHumans() }}</span>
                        <i class="fas fa-check-circle notification-item-read-icon"></i>
                    </div>
                </a>
            @empty
                <div class="no-notifications-bar">لا توجد إشعارات لعرضها.</div>
            @endforelse
        @endif
    </div>
    <div class="notification-bar-footer">
        <a href="{{ route('listener.notifications.index') }}" class="view-all-btn">
            عرض كل الإشعارات <i class="fas fa-arrow-left"></i>
        </a>
    </div>
</div>

<div id="audioPlayerContainer" class="fixed-bottom bg-light p-3 shadow-lg" style="display: none; z-index: 9998;">
    <h6 id="nowPlayingTitle" class="mb-2">يعمل الآن:</h6>
    <audio id="audio-player" controls class="w-100" data-audiobook-id=""></audio>
</div>

<button id="voice-command-btn" style="position: fixed; bottom: 100px; right: 30px; width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(145deg, #5c67e3, #8f6ed5); color: white; border: none; font-size: 24px; cursor: pointer; z-index: 9999; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2); transition: all 0.3s ease;">🎤</button>
<div id="voice-feedback" style="position: fixed; bottom: 170px; right: 30px; background-color: rgba(0,0,0,0.8); color: white; padding: 10px 15px; border-radius: 8px; display: none; z-index: 10000; box-shadow: 0 4px 15px rgba(0,0,0,0.2);"></div>

<div class="container">
    <h2 class="section-title">لوحة تحكم المستمع</h2>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center p-4 bg-primary text-white shadow-lg">
                <i class="fas fa-star fa-3x mb-3"></i>
                <h5 class="card-title">إجمالي التقييمات</h5>
                <p class="card-text fs-2 fw-bold">{{ $userStats['total_ratings'] }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center p-4 bg-success text-white shadow-lg">
                <i class="fas fa-comments fa-3x mb-3"></i>
                <h5 class="card-title">إجمالي التعليقات</h5>
                <p class="card-text fs-2 fw-bold">{{ $userStats['total_comments'] }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center p-4 bg-info text-white shadow-lg">
                <i class="fas fa-headphones-alt fa-3x mb-3"></i>
                <h5 class="card-title">مرات الاستماع</h5>
                <p class="card-text fs-2 fw-bold">{{ $userStats['total_listening_history'] }}</p>
            </div>
        </div>
    </div>

    @if(isset($recommendations) && !empty($recommendations))
        <hr class="section-divider">
        @foreach($recommendations as $key => $rec)
            @if(isset($rec['books']) && $rec['books']->isNotEmpty())
                <div class="recommendation-section">
                    <h3 class="recommendation-title">{{ $rec['title'] }}</h3>
                    <div class="recommendation-carousel">
                        @foreach($rec['books'] as $book)
                            <a href="{{ route('listener.audiobook.show', $book->id) }}" class="rec-card">
                                <img src="{{ $book->cover_image_path ? asset('storage/' . $book->cover_image_path) : asset('images/audio-placeholder.png') }}" alt="{{ $book->title }}" class="rec-card-img">
                                <div class="rec-card-body">
                                    <h4 class="rec-card-title">{{ $book->title }}</h4>
                                    <p class="rec-card-author">{{ $book->author }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    @endif

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @elseif(session('warning'))
        <div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}</div>
    @endif

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="recommendations-content" role="tabpanel"></div>
        <div class="tab-pane fade" id="latest-books-content" role="tabpanel">
            <form method="GET" action="{{ route('listener.dashboard') }}" class="mb-4 row align-items-center">
                <div class="col-md-4">
                    <label for="filterType" class="form-label"><i class="fas fa-filter"></i> تصفية حسب</label>
                    <select name="filterType" id="filterType" class="form-select">
                        <option value="">-- اختر نوع التصفية --</option>
                        <option value="title" {{ request('filterType') == 'title' ? 'selected' : '' }}>العنوان</option>
                        <option value="author" {{ request('filterType') == 'author' ? 'selected' : '' }}>المؤلف</option>
                        <option value="narrator" {{ request('filterType') == 'narrator' ? 'selected' : '' }}>الراوي</option>
                        <option value="language" {{ request('filterType') == 'language' ? 'selected' : '' }}>اللغة</option>
                        <option value="category" {{ request('filterType') == 'category' ? 'selected' : '' }}>الفئة</option>
                        <option value="publisher" {{ request('filterType') == 'publisher' ? 'selected' : '' }}>الناشر</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="filterValue" class="form-label"><i class="fas fa-search"></i> البحث</label>
                    <div class="search-container">
                        <input type="text" name="filterValue" id="filterValue" class="form-control" placeholder="ابحث عن كتاب..." value="{{ request('filterValue') }}" autocomplete="off">
                        <div id="search-suggestions"></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> بحث</button>
                </div>
            </form>

            <div class="top-buttons">
                <a href="{{ route('listener.playlists.create') }}" class="btn btn-success"><i class="fas fa-plus-circle"></i> إنشاء قائمة تشغيل جديدة</a>
                <a href="{{ route('listener.playlists.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> عرض قوائم التشغيل</a>
                <a href="{{ route('listener.subscribedPublishers') }}" class="btn btn-info"><i class="fas fa-users"></i> الناشرون المشترك بهم</a>
                <a href="{{ route('listener.downloadedAudio') }}" class="btn btn-show-downloaded"><i class="fas fa-download"></i> الملفات المحملة</a>
                <a href="{{ route('listener.my.achievements') }}" class="btn btn-warning"><i class="fas fa-trophy"></i> عرض إنجازاتي</a>
                @if (isset($firstAudioBook) && isset($bookmark))
                    <a href="{{ route('listener.playAudio', ['audioBook' => $firstAudioBook->id, 'startTime' => $bookmark->time ?? 0]) }}" class="btn btn-warning"><i class="fas fa-bookmark"></i> الانتقال للإشارة المرجعية</a>
                @else
                    <button class="btn btn-warning" disabled><i class="fas fa-exclamation-circle"></i> لا توجد كتب صوتية متاحة</button>
                @endif
            </div>

            <hr class="section-divider">
            <h3 class="section-title">أحدث الكتب الصوتية</h3>

            <div class="books-grid-row">
                @if(isset($audioBooks))
                    @forelse($audioBooks as $audioBook)
                        <div class="col col-lg-4 col-md-6">
                            <div class="card h-100 shadow-sm" id="audiobook-card-{{ $audioBook->id }}">
                                <div class="card-image-container">
                                    @php
                                        $hasImage = $audioBook->cover_image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($audioBook->cover_image_path);
                                    @endphp
                                    <img src="{{ $hasImage ? asset('storage/' . $audioBook->cover_image_path) : 'https://via.placeholder.com/300x200?text=No+Cover' }}" 
                                         class="card-img-top" 
                                         alt="غلاف الكتاب الصوتي"
                                         onerror="this.src='https://via.placeholder.com/300x200?text=No+Cover'; this.onerror=null;">
                                    <div class="card-overlay">
                                        <button type="button" class="btn-play play-button" data-file="{{ Storage::url($audioBook->file_path) }}" data-audiobook-id="{{ $audioBook->id }}">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title text-truncate">
                                        <a href="{{ route('listener.audiobook.show', $audioBook->id) }}" style="text-decoration: none; color: inherit;">
                                            <i class="fas fa-book"></i> {{ $audioBook->title }}
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted">{{ $audioBook->description }}</p>
                                    <div class="card-meta">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="badge bg-info"><i class="fas fa-tag"></i> {{ $audioBook->category->name ?? 'غير محدد' }}</span>
                                            <span class="badge bg-secondary"><i class="fas fa-language"></i> {{ $audioBook->language }}</span>
                                        </div>
                                        <div class="author-info">
                                            <small class="text-muted d-block"><i class="fas fa-user-edit"></i> المؤلف: {{ $audioBook->author }}</small>
                                            <small class="text-muted"><i class="fas fa-microphone"></i> الراوي: {{ $audioBook->narrator }}</small>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('listener.publisher.audioBooks', $audioBook->publisher->id) }}" class="btn btn-info w-100">
                                            <i class="fas fa-building"></i> كتب {{ $audioBook->publisher->name }}
                                        </a>
                                    </div>
                                    @php
                                        $publisher = $audioBook->publisher;
                                        $listener = Auth::user();
                                    @endphp
                                    @if($publisher)
                                        @if ($listener->subscriptions()->where('publisher_id', $publisher->id)->exists())
                                            <form action="{{ route('unsubscribe', $publisher->id) }}" method="POST" class="mt-3">
                                                @csrf
                                                <button type="submit" class="btn btn-danger w-100"><i class="fas fa-user-minus"></i> إلغاء الاشتراك</button>
                                            </form>
                                        @else
                                            <form action="{{ route('subscribe', $publisher->id) }}" method="POST" class="mt-3">
                                                @csrf
                                                <button type="submit" class="btn btn-success w-100"><i class="fas fa-user-plus"></i> اشتراك</button>
                                            </form>
                                        @endif
                                    @endif
                                    <div class="mt-3">
                                        <div class="rating-container">
                                            <div class="rating-stars" data-audiobook-id="{{ $audioBook->id }}">
                                                @php
                                                    $userRatingValue = $audioBook->ratings->where('listener_id', Auth::id())->first()->rating ?? 0;
                                                @endphp
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <span class="fa fa-star {{ $i <= $userRatingValue ? 'text-warning' : 'text-muted' }}" data-rating="{{ $i }}"></span>
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="remove-rating-container mt-2" style="height: 31px;">
                                            @if($userRatingValue > 0)
                                                <button class="btn btn-danger btn-sm remove-rating-btn" data-audiobook-id="{{ $audioBook->id }}"><i class="fas fa-trash"></i> حذف التقييم</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                                    <div class="action-buttons">
                                        <button type="button" class="btn btn-sm btn-primary play-button" data-file="{{ Storage::url($audioBook->file_path) }}" data-audiobook-id="{{ $audioBook->id }}">
                                            <i class="fas fa-play"></i> تشغيل
                                        </button>
                                        <a href="{{ route('listener.download', $audioBook) }}" class="btn btn-info btn-sm download-btn" data-audiobook-id="{{ $audioBook->id }}"><i class="fas fa-download"></i> تحميل</a>
                                    </div>
                                    <form action="{{ route('listener.playlist.addAudio') }}" method="POST" class="playlist-form">
                                        @csrf
                                        <input type="hidden" name="audioBookId" value="{{ $audioBook->id }}">
                                        <div class="form-group">
                                            <select name="playlistId" class="form-select">
                                                <option value="">اختر قائمة التشغيل</option>
                                                @if(isset($playlists))
                                                    @foreach($playlists as $playlist)
                                                        <option value="{{ $playlist->id }}">{{ $playlist->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-plus"></i> إضافة للقائمة</button>
                                    </form>
                                </div>
                                @auth
                                    <div class="comment-section p-3">
                                        <form action="{{ route('listener.comments.add', $audioBook->id) }}" method="POST" class="mt-3">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label"><i class="fas fa-comment"></i> إضافة تعليق</label>
                                                <textarea name="comment" class="form-control" placeholder="اكتب تعليقك هنا..." rows="3"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-paper-plane"></i> إرسال التعليق</button>
                                        </form>
                                    </div>
                                @endauth
                                <a href="{{ route('listener.comments.show', $audioBook->id) }}" class="btn btn-info w-100 mt-3 rounded-0 rounded-bottom"><i class="fas fa-comments"></i> عرض التعليقات</a>

                                {{-- ====================================================== --}}
                                {{-- ==   ▼▼▼ هذا هو الإصلاح النهائي لزر الإبلاغ ▼▼▼      == --}}
                                {{-- ====================================================== --}}
                                <button type="button" class="btn btn-danger btn-sm w-100 rounded-0 rounded-bottom"
                                        onclick="openGlobalReportModal('App\\Models\\AudioBook', {{ $audioBook->id }}, '{{ e($audioBook->title) }}')">
                                    <i class="fas fa-flag"></i> إبلاغ عن الكتاب
                                </button>
                                {{-- ====================================================== --}}
                                {{-- ==   ▲▲▲ نهاية الإصلاح ▲▲▲                           == --}}
                                {{-- ====================================================== --}}

                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center">لا توجد كتب صوتية متاحة حالياً.</div>
                        </div>
                    @endforelse
                @endif
            </div>
        </div>
    </div>

    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
            <p>جاري التحميل...</p>
        </div>
    </div>
</div>

{{-- ====================================================================== --}}
{{-- ==   ▼▼▼ النافذة المنبثقة (Modal) التي كانت مفقودة ▼▼▼             == --}}
{{-- ====================================================================== --}}
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="reportForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel">إبلاغ عن محتوى</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="reportableType" name="reportable_type">
                    <input type="hidden" id="reportableId" name="reportable_id">
                    <div class="mb-3">
                        <label for="reportReason" class="form-label">سبب الإبلاغ:</label>
                        <textarea class="form-control" id="reportReason" name="reason" rows="4" required minlength="10" placeholder="الرجاء تقديم وصف واضح للمشكلة..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger" id="submitReportBtn">إرسال الإبلاغ</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- ====================================================================== --}}
{{-- ==   ▲▲▲ نهاية النافذة المنبثقة ▲▲▲                                == --}}
{{-- ====================================================================== --}}


<!-- Report Modal -->

{{-- ▼▼▼ هذا هو الكود الكامل والنهائي الذي يجب أن يكون في نهاية dashboard.blade.php ▼▼▼ --}}
<script>
    // =================================================================
    //          1. تعريف دالة الإبلاغ العامة (هذا هو الجزء الجديد والمهم)
    // =================================================================
    function openGlobalReportModal(type, id, name) {
        const reportModalElement = document.getElementById('reportModal');
        if (!reportModalElement) {
            console.error('Modal element #reportModal not found!');
            return;
        }
        // تأكد من أن مكتبة Bootstrap محملة
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap is not loaded! Make sure bootstrap.bundle.min.js is included.');
            alert('حدث خطأ في تحميل المكونات. الرجاء تحديث الصفحة.');
            return;
        }
        const reportModal = new bootstrap.Modal(reportModalElement);
        document.getElementById('reportModalLabel').textContent = `إبلاغ عن: "${name}"`;
        document.getElementById('reportableType').value = type;
        document.getElementById('reportableId').value = id;
        reportModal.show();
    }

    // =================================================================
    //          2. الكود الذي يشتغل عند تحميل الصفحة
    // =================================================================
    document.addEventListener('DOMContentLoaded', () => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // --- معالجة إرسال فورم الإبلاغ (هذا أيضاً جديد ومهم) ---
        const reportForm = document.getElementById('reportForm');
        if (reportForm) {
            reportForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const submitBtn = document.getElementById('submitReportBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> جاري الإرسال...`;

                fetch("{{ route('listener.reports.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        reportable_type: document.getElementById('reportableType').value,
                        reportable_id: document.getElementById('reportableId').value,
                        reason: document.getElementById('reportReason').value
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    const modal = bootstrap.Modal.getInstance(reportForm.closest('.modal'));
                    modal.hide();
                    Swal.fire({ icon: 'success', title: 'تم إرسال بلاغك!', text: data.message || 'شكراً لك.', confirmButtonText: 'حسناً' });
                })
                .catch(errorData => {
                    const modal = bootstrap.Modal.getInstance(reportForm.closest('.modal'));
                    modal.hide();
                    Swal.fire({ icon: 'error', title: 'حدث خطأ', text: errorData.message || 'فشل الاتصال بالخادم.', confirmButtonText: 'موافق' });
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'إرسال الإبلاغ';
                    reportForm.reset();
                });
            });
        }

        // --- الكود الأصلي الذي كان لديك ويعمل بشكل صحيح ---
        const audioPlayer = document.getElementById('audio-player');
        const audioPlayerContainer = document.getElementById('audioPlayerContainer');
        const nowPlayingTitle = document.getElementById('nowPlayingTitle');
        const loadingOverlay = document.getElementById('loadingOverlay');
        const voiceBtn = document.getElementById('voice-command-btn');
        const voiceFeedback = document.getElementById('voice-feedback');
        const searchInput = document.getElementById('filterValue');
        const suggestionsContainer = document.getElementById('search-suggestions');

        document.body.addEventListener('click', function(event) {
            const target = event.target;
            if (target.classList.contains('fa-star') && target.closest('.rating-stars')) {
                const container = target.closest('.rating-stars');
                submitRating(container.dataset.audiobookId, target.dataset.rating);
            }
            if (target.matches('.remove-rating-btn')) {
                event.preventDefault();
                removeRating(target.dataset.audiobookId);
            }
            if (target.matches('.play-button, .play-button *')) {
                event.preventDefault();
                const button = target.closest('.play-button');
                const fileUrl = button.dataset.file;
                const audioBookId = button.dataset.audiobookId;
                const card = button.closest('.card');
                const title = card.querySelector('.card-title').textContent.trim();
                if (fileUrl) {
                    if(nowPlayingTitle) nowPlayingTitle.textContent = `يعمل الآن: ${title}`;
                    if(audioPlayer) {
                        audioPlayer.src = fileUrl;
                        audioPlayer.setAttribute('data-audiobook-id', audioBookId);
                    }
                    if(audioPlayerContainer) audioPlayerContainer.style.display = 'block';
                    showNotification('تم تحضير الكتاب. استخدم المشغل بالأسفل أو قل "تشغيل"', 'success');
                    logListeningHistory(audioBookId);
                }
            }
        });

        function submitRating(audioBookId, rating) {
            showLoading();
            fetch(`/listener/rate/${audioBookId}`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ rating: rating }) })
            .then(res => res.json()).then(data => { if (data.success) { showNotification('تم تسجيل تقييمك بنجاح!', 'success'); updateRatingUI(audioBookId, rating); } else { showNotification(data.message || 'فشل تسجيل التقييم.', 'error'); } }).catch(err => showNotification('فشل تسجيل التقييم.', 'error')).finally(hideLoading);
        }

        function removeRating(audioBookId) {
            if (!confirm('هل أنت متأكد أنك تريد حذف تقييمك؟')) return;
            showLoading();
            fetch(`/listener/audio-books/${audioBookId}/remove-rating`, { method: 'DELETE', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken } })
            .then(res => res.json()).then(data => { if (data.success) { showNotification('تم حذف التقييم بنجاح!', 'success'); updateRatingUI(audioBookId, 0); } else { showNotification(data.message || 'فشل حذف التقييم.', 'error'); } }).catch(err => showNotification('فشل حذف التقييم.', 'error')).finally(hideLoading);
        }

        function updateRatingUI(audioBookId, rating) {
            const card = document.getElementById(`audiobook-card-${audioBookId}`); if (!card) return; const starsContainer = card.querySelector('.rating-stars'); const removeContainer = card.querySelector('.remove-rating-container'); starsContainer.querySelectorAll('.fa-star').forEach(star => { star.classList.toggle('text-warning', star.dataset.rating <= rating); star.classList.toggle('text-muted', star.dataset.rating > rating); }); removeContainer.innerHTML = ''; if (rating > 0) { const removeButtonHTML = `<button class="btn btn-danger btn-sm remove-rating-btn" data-audiobook-id="${audioBookId}"><i class="fas fa-trash"></i> حذف التقييم</button>`; removeContainer.innerHTML = removeButtonHTML; }
        }

        function logListeningHistory(audioBookId) {
            fetch("{{ route('listener.log.history') }}", { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ audio_book_id: audioBookId }) })
            .then(res => res.json()).then(data => { if (data.success && data.new_achievements && data.new_achievements.length > 0) { data.new_achievements.forEach(ach => showNotification(`🎉 إنجاز جديد! لقد حصلت على: "${ach.name}"`, 'success')); } }).catch(err => console.error('Log history failed:', err));
        }

        function showLoading() { if(loadingOverlay) loadingOverlay.style.display = 'flex'; }
        function hideLoading() { if(loadingOverlay) loadingOverlay.style.display = 'none'; }

        function showNotification(message, type = 'success') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: type,
                    title: message,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            } else {
                alert(message);
            }
        }

        function showVoiceFeedback(message) {
            if(voiceFeedback) { voiceFeedback.textContent = message; voiceFeedback.style.display = 'block'; setTimeout(() => { voiceFeedback.style.display = 'none'; }, 4000); }
        }

        if (window.SpeechRecognition || window.webkitSpeechRecognition) {
            const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
            recognition.lang = 'ar-SA';
            recognition.interimResults = false;
            if(voiceBtn) { voiceBtn.addEventListener('click', () => { showVoiceFeedback('استمع الآن...'); voiceBtn.style.backgroundColor = '#dc3545'; try { recognition.start(); } catch(e) { console.error("Voice recognition start error:", e); } }); }
            recognition.onresult = (event) => { const command = event.results[0][0].transcript.trim().replace('.', ''); showVoiceFeedback(`سمعتك تقول: "${command}"`); if (!handleLocalCommand(command)) { sendCommandToLaravel({ command: command }); } };
            recognition.onspeechend = () => { recognition.stop(); if(voiceBtn) voiceBtn.style.backgroundColor = ''; };
            recognition.onerror = (event) => { showVoiceFeedback(`خطأ: ${event.error}`); if(voiceBtn) voiceBtn.style.backgroundColor = ''; };
        } else {
            if(voiceBtn) voiceBtn.style.display = 'none';
        }

        function handleLocalCommand(command) {
            const player = audioPlayer;
            const audioBookId = player ? player.getAttribute('data-audiobook-id') : null;
            const rateMatch = command.match(/تقييم (نجمة واحدة|نجمة|نجمتين|ثلاث نجوم|اربع نجوم|خمس نجوم|[1-5])/);
            if (rateMatch) { if (audioBookId) { const ratingsMap = { 'نجمة': 1, 'نجمة واحدة': 1, '1': 1, 'نجمتين': 2, '2': 2, 'ثلاث نجوم': 3, '3': 3, 'اربع نجوم': 4, '4': 4, 'خمس نجوم': 5, '5': 5 }; const ratingValue = ratingsMap[rateMatch[1]]; if (ratingValue !== undefined) { showVoiceFeedback(`جاري التقييم بـ ${ratingValue} نجوم...`); submitRating(audioBookId, ratingValue); } } else { showVoiceFeedback('الرجاء تشغيل كتاب أولاً لتقييمه.'); } return true; }
            const removeRateMatch = command.match(/احذف (نجمة|نجمتين|ثلاث|اربع|خمس)|حذف التقييم/);
            if (removeRateMatch) { if (audioBookId) { if (command.includes('حذف التقييم')) { showVoiceFeedback('جاري حذف التقييم بالكامل...'); removeRating(audioBookId); } else { const card = document.getElementById(`audiobook-card-${audioBookId}`); if (card) { const currentRating = card.querySelectorAll('.rating-stars .text-warning').length; if (currentRating > 0) { const ratingsToRemoveMap = { 'نجمة': 1, 'نجمتين': 2, 'ثلاث': 3, 'اربع': 4, 'خمس': 5 }; const starsToRemove = ratingsToRemoveMap[removeRateMatch[1]] || 0; const newRating = Math.max(0, currentRating - starsToRemove); showVoiceFeedback(`جاري تحديث التقييم إلى ${newRating} نجوم...`); submitRating(audioBookId, newRating); } else { showVoiceFeedback('لا يوجد تقييم لحذف نجوم منه.'); } } } } else { showVoiceFeedback('الرجاء تشغيل كتاب أولاً للتعامل مع تقييمه.'); } return true; }
            const commentMatch = command.match(/^(اكتب تعليق|اضف تعليق|تعليق) (.+)/i);
            if (commentMatch) { if (audioBookId) { const commentText = commentMatch[2].trim(); showVoiceFeedback('جاري إرسال تعليقك...'); sendCommandToLaravel({ action: 'addComment', audioBookId: audioBookId, comment: commentText }); } else { showVoiceFeedback('الرجاء تشغيل كتاب أولاً للتعليق عليه.'); } return true; }
            if (command.includes('إشارة مرجعية') || command.includes('حفظ مكان')) { if (audioBookId && player.src) { const currentTime = Math.floor(player.currentTime); showVoiceFeedback('جاري حفظ الإشارة المرجعية...'); sendCommandToLaravel({ action: 'addBookmark', audioBookId: audioBookId, time: currentTime }); } else { showVoiceFeedback('الرجاء تشغيل كتاب أولاً لحفظ مكانك.'); } return true; }
            if (command.includes('تشغيل')) { if (player && player.src) { player.play(); showVoiceFeedback('تم التشغيل'); } else { showVoiceFeedback('الرجاء تحضير كتاب أولاً.'); } return true; }
            if (command.includes('إيقاف')) { if (player && player.src && !player.paused) { player.pause(); showVoiceFeedback('تم الإيقاف المؤقت'); } return true; }
            if (command.includes('تحميل الكتاب') || command.includes('تنزيل الكتاب')) { if (audioBookId) { showVoiceFeedback('جاري بدء التحميل...'); sendCommandToLaravel({ action: 'download', audioBookId: audioBookId }); } else { showVoiceFeedback('الرجاء تشغيل كتاب أولاً لتحميله.'); } return true; }
            const isAddToPlaylistCommand = command.startsWith('إضافة إلى قائمة تشغيل');
            if (isAddToPlaylistCommand) { if (audioBookId) { const playlistName = command.replace('إضافة إلى قائمة تشغيل', '').trim(); if (!playlistName) { showVoiceFeedback('الرجاء تحديد اسم قائمة التشغيل.'); } else { sendCommandToLaravel({ action: 'addToPlaylist', playlistName: playlistName, audioBookId: audioBookId }); } } else { showVoiceFeedback('الرجاء تشغيل كتاب أولاً لتحديد الكتاب.'); } return true; }
            return false;
        }

        function sendCommandToLaravel(commandData) {
            fetch("{{ route('voice.command') }}", { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify(commandData) })
            .then(res => res.json()).then(data => {
                if (data.action === 'redirect') { showVoiceFeedback('جاري الانتقال...'); setTimeout(() => { window.location.href = data.url; }, 1000); }
                else if (data.action === 'download_success') { showVoiceFeedback('جاري بدء التحميل...'); const a = document.createElement('a'); a.style.display = 'none'; a.href = data.download_url; a.setAttribute('download', ''); document.body.appendChild(a); a.click(); document.body.removeChild(a); }
                else if (data.action === 'feedback' || data.action === 'error') { showVoiceFeedback(data.message); }
            }).catch(error => { showVoiceFeedback('فشل الاتصال بالخادم'); console.error('Laravel Command Error:', error); });
        }

        if (searchInput) {
            let debounceTimer;
            searchInput.addEventListener('keyup', () => {
                clearTimeout(debounceTimer);
                const query = searchInput.value;
                if (query.length < 2) { if(suggestionsContainer) { suggestionsContainer.innerHTML = ''; suggestionsContainer.style.display = 'none'; } return; }
                debounceTimer = setTimeout(() => {
                    fetch(`{{ route('api.search.suggestions') }}?query=${query}`)
                    .then(res => res.json()).then(data => {
                        if (suggestionsContainer) {
                            if (data.length > 0) { let suggestionsHTML = data.map(book => `<div class="suggestion-item"><a href="/listener/audiobook/${book.id}">${book.title}</a></div>`).join(''); suggestionsContainer.innerHTML = suggestionsHTML; suggestionsContainer.style.display = 'block'; }
                            else { suggestionsContainer.style.display = 'none'; }
                        }
                    }).catch(error => console.error('Error fetching suggestions:', error));
                }, 300);
            });
            document.addEventListener('click', (e) => { if (e.target !== searchInput && suggestionsContainer) { suggestionsContainer.style.display = 'none'; } });
        }
    });
</script>






</body>
</html>
