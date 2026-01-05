{{-- ====================================================================== --}}
{{-- ==   ملف الليآوت الرسمي والأساسي لكل صفحات الناشر                    == --}}
{{-- ==   File: resources/views/layouts/publisher.blade.php              == --}}
{{-- ====================================================================== --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- العنوان سيأتي من الصفحات الفرعية، مع عنوان افتراضي --}}
    <title>@yield('title', 'لوحة تحكم الناشر') - {{ config('app.name', 'Laravel') }}</title>

    {{-- روابط المكتبات والخطوط --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- الـ CSS الأساسي للوحة التحكم (مأخوذ من تصميمك الرائع ) --}}
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100% );
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --info-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --shadow-light: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            --shadow-heavy: 0 15px 35px rgba(0, 0, 0, 0.1);
            --bg-color: #f0f2f5;
            --card-bg: rgba(255, 255, 255, 0.9);
            --card-border: rgba(0, 0, 0, 0.05);
            --text-color: #1a202c;
            --text-muted: #718096;
            --navbar-bg: rgba(255, 255, 255, 0.7);
        }
        html[data-bs-theme='dark'] {
            --bg-color: #1a202c;
            --card-bg: rgba(26, 32, 44, 0.7);
            --card-border: rgba(255, 255, 255, 0.1);
            --text-color: #edf2f7;
            --text-muted: #a0aec0;
            --navbar-bg: rgba(26, 32, 44, 0.6);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s ease, color 0.3s ease;
            min-height: 100vh;
            overflow-x: hidden;
        }
        .navbar {
            background: var(--navbar-bg);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--card-border);
            box-shadow: var(--shadow-light);
            position: sticky; top: 0; z-index: 1020;
        }
        .navbar-brand { font-weight: 900; font-size: 1.8rem; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; text-shadow: 0 0 30px rgba(102, 126, 234, 0.5); }
        .nav-link { font-weight: 600; color: var(--text-color) !important; transition: all 0.3s ease; position: relative; }
        .nav-link:hover { color: var(--text-color) !important; transform: translateY(-2px); }
        .main-container { position: relative; z-index: 10; padding: 3rem 0; }
        .dropdown-menu { background: var(--navbar-bg); backdrop-filter: blur(20px); border: 1px solid var(--card-border); border-radius: 15px; box-shadow: var(--shadow-light); padding: 1rem 0; }
        .dropdown-item { font-weight: 600; padding: 0.8rem 1.5rem; color: var(--text-color); transition: all 0.3s ease; }
        .dropdown-item:hover { background: var(--primary-gradient); color: white; }
        .notification-bell { font-size: 1.5rem; color: var(--text-color) !important; }
        .notification-count { position: absolute; top: -5px; right: -10px; background: #e74c3c; color: white; border-radius: 50%; width: 22px; height: 22px; font-size: 0.8rem; font-weight: bold; display: flex; align-items: center; justify-content: center; border: 2px solid var(--navbar-bg); }
        .notification-dropdown { min-width: 350px; padding: 0; }
        .notification-header { padding: 1rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--card-border); }
        .notification-header h6 { margin: 0; font-weight: 700; color: var(--text-color); }
        .notification-header .mark-as-read { font-size: 0.8rem; color: var(--text-muted); text-decoration: none; }
        .notification-body { max-height: 300px; overflow-y: auto; }
        .notification-item { display: flex; align-items: center; padding: 1rem; border-bottom: 1px solid var(--card-border); text-decoration: none; }
        .notification-item.unread { background-color: rgba(102, 126, 234, 0.1); }
        .notification-icon { font-size: 1.2rem; width: 40px; text-align: center; color: #4facfe; }
        .notification-content { flex-grow: 1; color: var(--text-color); }
        .notification-content p { margin: 0; font-size: 0.9rem; }
        .notification-time { font-size: 0.75rem; color: var(--text-muted); margin-top: 4px; }
        .notification-footer { padding: 1rem; text-align: center; border-top: 1px solid var(--card-border); }
        .notification-footer a { font-weight: 700; color: var(--text-color); text-decoration: none; }
        .no-notifications { padding: 2rem; text-align: center; color: var(--text-muted); }
        .theme-switch-wrapper { display: flex; align-items: center; }
        .theme-switch { display: inline-block; height: 24px; position: relative; width: 50px; }
        .theme-switch input { display:none; }
        .slider { background-color: #ccc; bottom: 0; cursor: pointer; left: 0; position: absolute; right: 0; top: 0; transition: .4s; }
        .slider:before { background-color: #fff; bottom: 4px; content: ""; height: 16px; left: 4px; position: absolute; transition: .4s; width: 16px; }
        input:checked + .slider { background: var(--primary-gradient); }
        input:checked + .slider:before { transform: translateX(26px); }
        .slider.round { border-radius: 34px; }
        .slider.round:before { border-radius: 50%; }
        .theme-switch-wrapper .fa-sun, .theme-switch-wrapper .fa-moon { margin: 0 10px; color: var(--text-color); }
        /* تنسيقات مقترحة لزر العودة */
.btn-outline-primary {
    border-width: 2px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.float-end {
    float: right;
}

    </style>

    {{-- مكان مخصص لإضافة CSS إضافي من الصفحات الفرعية --}}
    @stack('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="{{ route('publisher.dashboard') }}"><i class="fas fa-book-open me-2"></i>{{ config('app.name', 'Laravel') }}</a>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item me-3">
                            <div class="theme-switch-wrapper">
                                <i class="fas fa-sun"></i>
                                <label class="theme-switch" for="theme-toggle-checkbox">
                                    <input type="checkbox" id="theme-toggle-checkbox" />
                                    <div class="slider round"></div>
                                </label>
                                <i class="fas fa-moon"></i>
                            </div>
                        </li>

                        {{-- قائمة الإشعارات --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell notification-bell"></i>
                                @if(Auth::user()->unreadNotifications->count() > 0)
                                    <span class="notification-count">{{ Auth::user()->unreadNotifications->count() }}</span>
                                @endif
                            </a>
                            <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                                <div class="notification-header">
                                    <h6>الإشعارات</h6>
                                    @if(Auth::user()->unreadNotifications->count() > 0)
                                    <form action="{{ route('publisher.notifications.markAllAsRead') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-link mark-as-read p-0">تمييز الكل كمقروء</button>
                                    </form>
                                    @endif
                                </div>
                                <div class="notification-body">
                                    @forelse (Auth::user()->notifications()->take(5)->get() as $notification)
                                        <a href="{{ route('publisher.notifications.markOneAsRead', $notification->id) }}" class="notification-item {{ !$notification->is_read ? 'unread' : '' }}">
                                            <div class="notification-icon"><i class="{{ $notification->icon ?? 'fas fa-info-circle' }}"></i></div>
                                            <div class="notification-content">
                                                <p>{{ $notification->message }}</p>
                                                <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="no-notifications">لا توجد إشعارات جديدة.</div>
                                    @endforelse
                                </div>
                                <div class="notification-footer">
                                    <a href="{{ route('publisher.notifications.index') }}">عرض كل الإشعارات</a>
                                </div>
                            </div>
                        </li>

                        {{-- قائمة المستخدم --}}
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <i class="fas fa-user-circle me-2"></i>{{ Auth::user()->name }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>{{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="main-container">
            {{-- ▼▼▼ هذا هو المكان الذي سيتم فيه حقن محتوى كل صفحة ▼▼▼ --}}
            @yield('content')
            {{-- ▲▲▲ انتهى مكان حقن المحتوى ▲▲▲ --}}
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>

    {{-- كود تبديل الإضاءة (الآن موجود في مكان واحد فقط ) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleSwitch = document.getElementById('theme-toggle-checkbox');
            const currentTheme = localStorage.getItem('theme');

            function applyTheme(theme) {
                document.documentElement.setAttribute('data-bs-theme', theme);
                if (theme === 'dark') {
                    toggleSwitch.checked = true;
                } else {
                    toggleSwitch.checked = false;
                }
            }

            if (currentTheme) {
                applyTheme(currentTheme);
            } else {
                applyTheme('light'); // Default theme
            }

            function switchTheme(e) {
                const theme = e.target.checked ? 'dark' : 'light';
                document.documentElement.setAttribute('data-bs-theme', theme);
                localStorage.setItem('theme', theme);
                // إرسال حدث مخصص لإعلام المكونات الأخرى (مثل المخططات)
                document.dispatchEvent(new Event('themeChanged'));
            }
            toggleSwitch.addEventListener('change', switchTheme, false);
        });
    </script>

    {{-- مكان مخصص لإضافة JavaScript إضافي من الصفحات الفرعية --}}
    @stack('scripts')
</body>
</html>
