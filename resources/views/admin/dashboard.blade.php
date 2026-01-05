<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مركز قيادة الأدمن</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* === BODY AND BACKGROUND === */
        body {
            margin: 0;
            font-family: 'Cairo', sans-serif;
            color: #f0f0f0;
            background: linear-gradient(-45deg, #0b1120, #1c2a4a, #3a506b, #1e3b5c  );
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* === STICKY NAVBAR === */
        .sticky-navbar { position: sticky; top: 0; left: 0; width: 100%; background: rgba(11, 17, 32, 0.85); backdrop-filter: blur(10px); padding: 15px 0; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3); z-index: 1000; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .navbar-container { max-width: 1800px; margin: auto; padding: 0 40px; display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { font-size: 1.5rem; font-weight: 700; color: #ecf0f1; }
        .navbar-actions { display: flex; align-items: center; }
        .navbar-actions .action-button, .navbar-actions form { margin: 0 8px; }
        .action-button { display: inline-flex; align-items: center; gap: 8px; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; font-size: 0.9rem; color: white; transition: all 0.3s ease; border: none; cursor: pointer; }
        .btn-manage-users { background-color: #2980b9; } .btn-manage-users:hover { background-color: #3498db; }
        .btn-logout { background-color: #c0392b; } .btn-logout:hover { background-color: #e74c3c; }
        .btn-manage-books { background-color: #8e44ad; } .btn-manage-books:hover { background-color: #9b59b6; }
        .btn-manage-categories { background-color: #27ae60; } .btn-manage-categories:hover { background-color: #2ecc71; }
        .btn-manage-plans { background-color: #d35400; } .btn-manage-plans:hover { background-color: #e67e22; }
        .btn-settings { background-color: #f39c12; } .btn-settings:hover { background-color: #f1c40f; }
        .notifications-container { position: relative; margin: 0 15px; }
        .notification-bell { font-size: 1.5rem; color: #ecf0f1; cursor: pointer; }
        .notification-count { position: absolute; top: -5px; right: -10px; background-color: #e74c3c; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.75rem; font-weight: bold; border: 2px solid #0b1120; }
        .notifications-dropdown { display: none; position: absolute; top: 50px; left: -150px; width: 350px; background: #1c2a4a; border-radius: 10px; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5); border: 1px solid rgba(255, 255, 255, 0.15); z-index: 1100; max-height: 400px; overflow-y: auto; }
        .dropdown-header { padding: 15px; font-weight: bold; border-bottom: 1px solid rgba(255, 255, 255, 0.1); display: flex; justify-content: space-between; align-items: center; }
        .notification-item { display: flex; align-items: center; padding: 15px; border-bottom: 1px solid rgba(255, 255, 255, 0.05); text-decoration: none; color: #ecf0f1; }
        .notification-item:hover { background-color: #3a506b; }
        .notification-icon { font-size: 1.2rem; margin-left: 15px; color: #3498db; }
        .notification-content p { margin: 0; font-size: 0.9rem; }
        .notification-content .time { font-size: 0.75rem; color: #bdc3c7; margin-top: 4px; }
        .no-notifications { padding: 20px; text-align: center; color: #95a5a6; }

        /* === التصميم الثوري الجديد === */
        .main-container { padding: 40px; max-width: 1800px; margin: auto; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { font-size: 2.5rem; margin: 0; }
        .dashboard-grid { display: grid; grid-template-columns: repeat(12, 1fr); grid-auto-rows: minmax(100px, auto); gap: 25px; }
        .card { background: rgba(255, 255, 255, 0.08); border-radius: 15px; padding: 25px; border: 1px solid rgba(255, 255, 255, 0.1); display: flex; flex-direction: column; }
        .card-header { margin: -25px -25px 20px -25px; padding: 15px 25px; font-size: 1.2rem; font-weight: 600; color: #ecf0f1; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 10px; }
        .card-stats-summary { grid-column: span 12; }
        .card-recent-activity { grid-column: span 5; grid-row: span 2; }
        .card-popular-books { grid-column: span 4; }
        .card-active-publishers { grid-column: span 3; }
        .card-roles-chart { grid-column: span 4; }
        .stats-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 20px; }
        .mini-stat { text-align: center; }
        .mini-stat .stat-number { font-size: 2rem; font-weight: 700; color: #3498db; }
        .mini-stat .stat-title { font-size: 0.9rem; color: #bdc3c7; margin-top: 5px; }
        .mini-stat.highlight .stat-number { color: #f39c12; }
        .smart-list { list-style: none; padding: 0; margin: 0; flex-grow: 1; display: flex; flex-direction: column; }
        .smart-list li { display: flex; align-items: center; padding: 12px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.07); }
        .smart-list li:last-child { border-bottom: none; }
        .list-item-icon { width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-left: 15px; font-size: 0.9rem; }
        .list-item-content { flex-grow: 1; }
        .list-item-title { font-weight: 600; color: #ecf0f1; font-size: 0.95rem; }
        .list-item-meta { font-size: 0.8rem; color: #95a5a6; }
        .list-item-value { font-weight: 700; color: #3498db; }
        .bg-icon-user { background-color: #3498db; }
        .bg-icon-book { background-color: #9b59b6; }
        .bg-icon-publisher { background-color: #2ecc71; }
        .bg-icon-rating { background-color: #f1c40f; }
        @media (max-width: 1400px) {
            .card-recent-activity { grid-column: span 12; grid-row: span 1; }
            .card-popular-books { grid-column: span 6; }
            .card-active-publishers { grid-column: span 6; }
            .card-roles-chart { grid-column: span 12; }
        }
        @media (max-width: 768px) {
            .dashboard-grid { grid-template-columns: 1fr; }
            .card { grid-column: span 1 !important; }
        }
    </style>
</head>
<body>

    <div class="sticky-navbar">
        <div class="navbar-container">
            <div class="navbar-brand"><i class="fas fa-user-shield"></i> {{ settings('site_name', 'لوحة تحكم الأدمن') }}</div>
            <div class="navbar-actions">
                <a href="{{ route('admin.users.index') }}" class="action-button btn-manage-users"><i class="fas fa-users"></i> إدارة المستخدمين</a>
                <a href="{{ route('admin.audiobooks.index') }}" class="action-button btn-manage-books"><i class="fas fa-book"></i> إدارة الكتب</a>
               <a href="{{ route('admin.reports.index') }}" class="action-button" style="background-color: #e67e22;"><i class="fas fa-flag"></i> إدارة الإبلاغات</a>
                <a href="{{ route('admin.categories.index') }}" class="action-button btn-manage-categories"><i class="fas fa-tags"></i> إدارة الفئات</a>
                <a href="{{ route('admin.plans.index') }}" class="action-button btn-manage-plans"><i class="fas fa-dollar-sign"></i> إدارة الخطط</a>
                <a href="{{ route('admin.settings.index') }}" class="action-button btn-settings"><i class="fas fa-cog"></i> الإعدادات</a>
{{-- ====================================================== --}}
{{-- ==      ▼▼▼ زر إدارة الإعلانات (تصميم احترافي) ▼▼▼      == --}}
{{-- ====================================================== --}}

<a href="{{ route('admin.advertisements.index') }}" class="btn btn-lg shadow-sm" style="
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 12px 24px;
    border-radius: 50px;
    font-weight: 700;
    font-family: 'Tajawal', sans-serif;
    color: white;
    background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 20px rgba(0,0,0,0.2)';"
   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)';">

    <i class="fas fa-ad"></i>
    <span>إدارة الإعلانات</span>
</a>

{{-- ====================================================== --}}
{{-- ==      ▲▲▲ نهاية زر إدارة الإعلانات ▲▲▲             == --}}
{{-- ====================================================== --}}


                {{-- ▼▼▼ هذا هو زر تسجيل الخروج الجديد والمُدمج ▼▼▼ --}}
                <a href="#" id="admin-logout-button" class="action-button btn-logout">
                    <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                </a>
                {{-- ▲▲▲ انتهى الزر الجديد ▲▲▲ --}}
{{-- ▼▼▼ هذا هو الكود الجديد والكامل لقسم الإشعارات ▼▼▼ --}}
<div class="notifications-container">
    <i class="fas fa-bell notification-bell" id="notificationBell">
        {{-- المتغيرات التي أرسلتها من الكنترولر صحيحة --}}
        @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
            <span class="notification-count" id="notificationCount">{{ $unreadNotificationsCount }}</span>
        @endif
    </i>
    <div class="notifications-dropdown" id="notificationsDropdown">
        <div class="dropdown-header">
            <span>الإشعارات ({{ $unreadNotificationsCount ?? 0 }})</span>
            @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                <form action="{{ route('admin.notifications.markAllAsRead') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn bg-transparent border-0 p-0" title="تمييز الكل كمقروء"><i class="fas fa-check-double text-success"></i></button>
                </form>
            @endif
        </div>
        <div class="dropdown-body">
            {{-- المتغير الذي أرسلته من الكنترولر صحيح --}}
            @forelse ($notifications ?? [] as $notification)
                <a href="{{ $notification->data['link'] ?? '#' }}" class="notification-item">
                    {{-- سنقرأ الأيقونة من حقل data --}}
                    <i class="{{ $notification->data['icon'] ?? 'fas fa-info-circle' }} notification-icon"></i>
                    <div class="notification-content">
                        {{-- ▼▼▼ هذا هو السطر الأهم الذي تم تصحيحه ▼▼▼ --}}
                        <p>{{ $notification->data['message'] ?? 'إشعار بدون محتوى' }}</p>
                        <span class="time">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                </a>
            @empty
                <div class="no-notifications">لا توجد إشعارات جديدة</div>
            @endforelse
            <div style="padding: 10px; text-align: center; border-top: 1px solid rgba(255, 255, 255, 0.1);"><a href="{{ route('admin.notifications.index') }}" style="color: #3498db; text-decoration: none; font-weight: bold;">عرض كل الإشعارات</a></div>
        </div>
    </div>
</div>

            </div>
        </div>
    </div>

    {{-- ▼▼▼ هذا هو النموذج المخفي الذي يقوم بعملية تسجيل الخروج الفعلية ▼▼▼ --}}
    <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    {{-- ▲▲▲ انتهى النموذج المخفي ▲▲▲ --}}

    <div class="main-container">
        <div class="header">
            <h1>مركز القيادة</h1>
        </div>

        <div class="dashboard-grid">
            <!-- === صف الإحصائيات المدمج === -->
            <div class="card card-stats-summary">
                <div class="card-header"><i class="fas fa-wave-square"></i> نظرة عامة ونبض النظام</div>
                <div class="stats-container">
                    <div class="mini-stat"><div class="stat-number">{{ $stats['totalUsers'] }}</div><div class="stat-title">إجمالي المستخدمين</div></div>
                    <div class="mini-stat"><div class="stat-number">{{ $stats['totalAudioBooks'] }}</div><div class="stat-title">إجمالي الكتب</div></div>
                    <div class="mini-stat highlight"><div class="stat-number">{{ $stats['pendingAudioBooks'] }}</div><div class="stat-title">كتب قيد المراجعة</div></div>
                    <div class="mini-stat"><div class="stat-number">{{ $stats['averageRating'] }} ★</div><div class="stat-title">متوسط التقييم</div></div>
                    <div class="mini-stat"><div class="stat-number">{{ $weeklyActivity['newUsers'] }}</div><div class="stat-title">مستخدمون جدد (7 أيام)</div></div>
                    <div class="mini-stat"><div class="stat-number">{{ $weeklyActivity['newBooks'] }}</div><div class="stat-title">كتب جديدة (7 أيام)</div></div>
                </div>
            </div>

            <!-- === قائمة أحدث الأنشطة === -->
            <div class="card card-recent-activity">
                <div class="card-header"><i class="fas fa-bolt"></i> أحدث الأنشطة في النظام</div>
                <ul class="smart-list">
                    @forelse($recentActivities as $activity)
                        @if($activity->type === 'user_registration')
                            <li>
                                <div class="list-item-icon bg-icon-user"><i class="fas fa-user-plus"></i></div>
                                <div class="list-item-content">
                                    <div class="list-item-title">تسجيل: {{ $activity->name }}</div>
                                    <div class="list-item-meta">{{ $activity->created_at->diffForHumans() }} - ({{ $activity->role === 'listener' ? 'مستمع' : 'ناشر' }})</div>
                                </div>
                            </li>
                        @elseif($activity->type === 'book_upload')
                            <li>
                                <div class="list-item-icon bg-icon-book"><i class="fas fa-book"></i></div>
                                <div class="list-item-content">
                                    <div class="list-item-title">كتاب جديد: {{ Str::limit($activity->title, 25) }}</div>
                                    <div class="list-item-meta">{{ $activity->created_at->diffForHumans() }} - بواسطة: {{ $activity->publisher->name ?? 'غير معروف' }}</div>
                                </div>
                            </li>
                        @endif
                    @empty
                        <li><div class="list-item-content"><div class="list-item-title">لا توجد أنشطة حديثة.</div></div></li>
                    @endforelse
                </ul>
            </div>

            <!-- === قائمة أكثر الكتب شعبية === -->
            <div class="card card-popular-books">
                <div class="card-header"><i class="fas fa-fire"></i> أكثر الكتب شعبية</div>
                <ul class="smart-list">
                    @forelse($popularAudioBooks as $book)
                        <li>
                            <div class="list-item-icon bg-icon-rating"><i class="fas fa-star"></i></div>
                            <div class="list-item-content">
                                <div class="list-item-title">{{ Str::limit($book->title, 30) }}</div>
                                <div class="list-item-meta">بواسطة: {{ $book->publisher->name ?? 'غير معروف' }}</div>
                            </div>
                            <div class="list-item-value">{{ number_format($book->ratings_avg_rating, 1) }} ★</div>
                        </li>
                    @empty
                        <li><div class="list-item-content"><div class="list-item-title">لا توجد كتب مقيمة بعد.</div></div></li>
                    @endforelse
                </ul>
            </div>

            <!-- === قائمة أكثر الناشرين نشاطاً === -->
            <div class="card card-active-publishers">
                <div class="card-header"><i class="fas fa-rocket"></i> أكثر الناشرين نشاطاً</div>
                <ul class="smart-list">
                    @forelse($activePublishers as $publisher)
                        <li>
                            <div class="list-item-icon bg-icon-publisher"><i class="fas fa-user-tie"></i></div>
                            <div class="list-item-content">
                                <div class="list-item-title">{{ $publisher->name }}</div>
                            </div>
                            <div class="list-item-value">{{ $publisher->audio_books_count }} كتاب</div>
                        </li>
                    @empty
                        <li><div class="list-item-content"><div class="list-item-title">لا يوجد ناشرون بعد.</div></div></li>
                    @endforelse
                </ul>
            </div>

            <!-- === مخطط توزيع الأدوار === -->
            <div class="card card-roles-chart">
                <div class="card-header"><i class="fas fa-pie-chart"></i> توزيع الأدوار</div>
                <div style="position: relative; height: 250px;">
                    <canvas id="rolesChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Chart.defaults.color = '#ecf0f1';
            Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.2)';

            // رسم توزيع الأدوار
            const rolesCtx = document.getElementById('rolesChart').getContext('2d');
            new Chart(rolesCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($roleChartLabels),
                    datasets: [{
                        data: @json($roleChartValues),
                        backgroundColor: ['#3498db', '#2ecc71'],
                        hoverOffset: 4,
                        borderColor: '#1c2a4a'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { padding: 20, font: { size: 14, weight: '600' } }
                        }
                    }
                }
            });

            // كود الإشعارات
            const notificationBell = document.getElementById('notificationBell');
            const notificationsDropdown = document.getElementById('notificationsDropdown');
            if (notificationBell) {
                notificationBell.addEventListener('click', function(event) {
                    event.stopPropagation();
                    notificationsDropdown.style.display = notificationsDropdown.style.display === 'block' ? 'none' : 'block';
                });
            }
            window.addEventListener('click', function(event) {
                if (notificationsDropdown && notificationsDropdown.style.display === 'block' && !notificationsDropdown.contains(event.target) && !notificationBell.contains(event.target)) {
                    notificationsDropdown.style.display = 'none';
                }
            });

            // ▼▼▼ هذا هو كود تفعيل زر تسجيل الخروج الجديد ▼▼▼
            const logoutButton = document.getElementById('admin-logout-button');
            const logoutForm = document.getElementById('admin-logout-form');
            if (logoutButton && logoutForm) {
                logoutButton.addEventListener('click', function(event) {
                    event.preventDefault();
                    logoutForm.submit();
                });
            }
            // ▲▲▲ انتهى كود التفعيل ▲▲▲
        });
    </script>

</body>
</html>
