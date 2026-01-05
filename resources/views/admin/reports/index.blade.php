<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    {{-- تم تغيير العنوان ليعكس محتوى الصفحة --}}
    <title>إدارة الإبلاغات</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- تم نسخ نفس الـ CSS من الداشبورد --}}
    <style>
        body{margin:0;font-family:'Cairo',sans-serif;color:#f0f0f0;background:linear-gradient(-45deg,#0b1120,#1c2a4a,#3a506b,#1e3b5c );background-size:400% 400%;animation:gradientBG 15s ease infinite}@keyframes gradientBG{0%{background-position:0 50%}50%{background-position:100% 50%}100%{background-position:0 50%}}.sticky-navbar{position:sticky;top:0;left:0;width:100%;background:rgba(11,17,32,.85);backdrop-filter:blur(10px);padding:15px 0;box-shadow:0 4px 15px rgba(0,0,0,.3);z-index:1000;border-bottom:1px solid rgba(255,255,255,.1)}.navbar-container{max-width:1800px;margin:auto;padding:0 40px;display:flex;justify-content:space-between;align-items:center}.navbar-brand{font-size:1.5rem;font-weight:700;color:#ecf0f1}.navbar-actions{display:flex;align-items:center}.navbar-actions .action-button,.navbar-actions form{margin:0 8px}.action-button{display:inline-flex;align-items:center;gap:8px;text-decoration:none;padding:10px 20px;border-radius:8px;font-weight:bold;font-size:.9rem;color:#fff;transition:all .3s ease;border:none;cursor:pointer}.btn-manage-users{background-color:#2980b9}.btn-manage-users:hover{background-color:#3498db}.btn-logout{background-color:#c0392b}.btn-logout:hover{background-color:#e74c3c}.btn-manage-books{background-color:#8e44ad}.btn-manage-books:hover{background-color:#9b59b6}.btn-manage-categories{background-color:#27ae60}.btn-manage-categories:hover{background-color:#2ecc71}.btn-manage-plans{background-color:#d35400}.btn-manage-plans:hover{background-color:#e67e22}.btn-settings{background-color:#f39c12}.btn-settings:hover{background-color:#f1c40f}.notifications-container{position:relative;margin:0 15px}.notification-bell{font-size:1.5rem;color:#ecf0f1;cursor:pointer}.notification-count{position:absolute;top:-5px;right:-10px;background-color:#e74c3c;color:#fff;border-radius:50%;padding:2px 6px;font-size:.75rem;font-weight:bold;border:2px solid #0b1120}.notifications-dropdown{display:none;position:absolute;top:50px;left:-150px;width:350px;background:#1c2a4a;border-radius:10px;box-shadow:0 8px 25px rgba(0,0,0,.5);border:1px solid rgba(255,255,255,.15);z-index:1100;max-height:400px;overflow-y:auto}.dropdown-header{padding:15px;font-weight:bold;border-bottom:1px solid rgba(255,255,255,.1);display:flex;justify-content:space-between;align-items:center}.notification-item{display:flex;align-items:center;padding:15px;border-bottom:1px solid rgba(255,255,255,.05);text-decoration:none;color:#ecf0f1}.notification-item:hover{background-color:#3a506b}.notification-icon{font-size:1.2rem;margin-left:15px;color:#3498db}.notification-content p{margin:0;font-size:.9rem}.notification-content .time{font-size:.75rem;color:#bdc3c7;margin-top:4px}.no-notifications{padding:20px;text-align:center;color:#95a5a6}.main-container{padding:40px;max-width:1800px;margin:auto}.header{text-align:center;margin-bottom:30px}.header h1{font-size:2.5rem;margin:0}.card{background:rgba(255,255,255,.08);border-radius:15px;padding:25px;border:1px solid rgba(255,255,255,.1);display:flex;flex-direction:column}.card-header{margin:-25px -25px 20px -25px;padding:15px 25px;font-size:1.2rem;font-weight:600;color:#ecf0f1;border-bottom:1px solid rgba(255,255,255,.1);display:flex;align-items:center;gap:10px}
        /* إضافة تنسيقات خاصة بصفحة البلاغات */
        .table{width:100%;color:#f0f0f0;border-collapse:collapse}.table th,.table td{padding:15px;text-align:right;border-bottom:1px solid rgba(255,255,255,.1)}.table th{font-weight:700;background-color:rgba(255,255,255,.1)}.table tbody tr:hover{background-color:rgba(255,255,255,.05)}.badge{padding:5px 10px;border-radius:20px;font-size:.8rem;font-weight:bold}.badge-info{background-color:#3498db}.badge-warning{background-color:#f39c12}.badge-secondary{background-color:#95a5a6}.badge-success{background-color:#2ecc71}.badge-danger{background-color:#e74c3c}.btn-sm{padding:5px 10px;font-size:.8rem}.btn-info{background-color:#3498db;color:#fff;border:none;border-radius:5px;cursor:pointer}.btn-danger{background-color:#e74c3c;color:#fff;border:none;border-radius:5px;cursor:pointer}.pagination{justify-content:center;display:flex;padding-left:0;list-style:none}.pagination .page-item .page-link{color:#3498db;background-color:transparent;border:1px solid #3498db;margin:0 5px;border-radius:5px}.pagination .page-item.active .page-link{z-index:3;color:#fff;background-color:#3498db;border-color:#3498db}.pagination .page-item.disabled .page-link{color:#95a5a6;pointer-events:none;background-color:transparent;border-color:#95a5a6}
        .form-control, .btn { border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.1); color: white; padding: 10px; }
        .btn-primary { background-color: #3498db; border: none; } .btn-secondary { background-color: #3a506b; border: none; }
    </style>
</head>
<body>

    {{-- ▼▼▼ قسم الـ Navbar العلوي (منسوخ كما هو) ▼▼▼ --}}
    <div class="sticky-navbar">
        <div class="navbar-container">
            <div class="navbar-brand"><i class="fas fa-user-shield"></i> {{ settings('site_name', 'لوحة تحكم الأدمن') }}</div>
            <div class="navbar-actions">
                <a href="{{ route('admin.users.index') }}" class="action-button btn-manage-users"><i class="fas fa-users"></i> إدارة المستخدمين</a>
                <a href="{{ route('admin.audiobooks.index') }}" class="action-button btn-manage-books"><i class="fas fa-book"></i> إدارة الكتب</a>

                {{-- ▼▼▼ هذا هو زر إدارة البلاغات (مع تظليل عند التفعيل) ▼▼▼ --}}
                <a href="{{ route('admin.reports.index') }}" class="action-button" style="background-color: #e67e22;"><i class="fas fa-flag"></i> إدارة الإبلاغات</a>

                <a href="{{ route('admin.categories.index') }}" class="action-button btn-manage-categories"><i class="fas fa-tags"></i> إدارة الفئات</a>
                <a href="{{ route('admin.plans.index') }}" class="action-button btn-manage-plans"><i class="fas fa-dollar-sign"></i> إدارة الخطط</a>
                <a href="{{ route('admin.settings.index') }}" class="action-button btn-settings"><i class="fas fa-cog"></i> الإعدادات</a>
                <a href="{{ route('admin.advertisements.index') }}" class="action-button" style="background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);"><i class="fas fa-ad"></i> إدارة الإعلانات</a>
                <a href="#" id="admin-logout-button" class="action-button btn-logout"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>

                {{-- قسم الإشعارات --}}
                <div class="notifications-container">
                    {{-- ... كود الإشعارات هنا ... --}}
                </div>
            </div>
        </div>
    </div>
    <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
    {{-- ▲▲▲ نهاية قسم الـ Navbar ▲▲▲ --}}


    {{-- ▼▼▼ هذا هو المحتوى الجديد الخاص بصفحة البلاغات ▼▼▼ --}}
    <div class="main-container">
        <div class="header">
            <h1>إدارة الإبلاغات</h1>
        </div>

        <div class="card">
            <div class="card-header"><i class="fas fa-list-ul"></i> قائمة الإبلاغات الواردة</div>

            {{-- قسم الفلترة --}}
            <form method="GET" action="{{ route('admin.reports.index') }}" class="mb-4" style="padding: 20px 0;">
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <select name="status" class="form-control">
                        <option value="">كل الحالات</option>
                        <option value="pending" {{ $selectedStatus == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                        <option value="reviewed" {{ $selectedStatus == 'reviewed' ? 'selected' : '' }}>تمت المراجعة</option>
                        <option value="rejected" {{ $selectedStatus == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                    </select>
                    <select name="reportable_type" class="form-control">
                        <option value="">كل الأنواع</option>
                        <option value="App\Models\AudioBook" {{ $selectedType == 'App\Models\AudioBook' ? 'selected' : '' }}>كتب صوتية</option>
                        <option value="App\Models\Comment" {{ $selectedType == 'App\Models\Comment' ? 'selected' : '' }}>تعليقات</option>
                    </select>
                    <div>
                        <button type="submit" class="btn btn-primary">فلترة</button>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">إعادة تعيين</a>
                    </div>
                </div>
            </form>

            {{-- جدول عرض البيانات --}}
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المُبلغ</th>
                            <th>نوع العنصر</th>
                            <th>العنصر المُبلغ عنه</th>
                            <th>الحالة</th>
                            <th>تاريخ الإبلاغ</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reports as $report)
                            <tr>
                                <td>{{ $report->id }}</td>
                                <td>{{ $report->user->name ?? 'مستخدم محذوف' }}</td>
                                <td>
                                    @if ($report->reportable_type == 'App\Models\AudioBook')
                                        <span class="badge badge-info">كتاب صوتي</span>
                                    @elseif ($report->reportable_type == 'App\Models\Comment')
                                        <span class="badge badge-warning">تعليق</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($report->reportable)
                                        @if ($report->reportable_type == 'App\Models\AudioBook')
                                            {{ $report->reportable->title }}
                                        @elseif ($report->reportable_type == 'App\Models\Comment')
                                            "{{ \Illuminate\Support\Str::limit($report->reportable->comment, 30) }}"
                                        @endif
                                    @else
                                        <span class="text-danger" style="color: #e74c3c;">عنصر محذوف</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($report->status == 'pending')
                                        <span class="badge badge-secondary">قيد المراجعة</span>
                                    @elseif ($report->status == 'reviewed')
                                        <span class="badge badge-success">تمت المراجعة</span>
                                    @elseif ($report->status == 'rejected')
                                        <span class="badge badge-danger">مرفوض</span>
                                    @endif
                                </td>
                                <td>{{ $report->created_at->format('Y-m-d H:i') }}</td>
                                <td style="display: flex; gap: 5px;">
                                    <a href="{{ route('admin.reports.show', $report->id) }}" class="btn btn-sm btn-info">عرض</a>
                                    <form action="{{ route('admin.reports.destroy', $report->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإبلاغ؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 20px;">لا توجد إبلاغات لعرضها.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- روابط التنقل بين الصفحات --}}
            <div style="margin-top: 20px;">
                {{ $reports->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    {{-- ▲▲▲ نهاية المحتوى الجديد ▲▲▲ --}}


    {{-- ▼▼▼ قسم الـ Scripts (منسوخ كما هو) ▼▼▼ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // كود تفعيل زر تسجيل الخروج
            const logoutButton = document.getElementById('admin-logout-button');
            const logoutForm = document.getElementById('admin-logout-form');
            if (logoutButton && logoutForm) {
                logoutButton.addEventListener('click', function(event) {
                    event.preventDefault();
                    logoutForm.submit();
                });
            }
        });
    </script>

</body>
</html>
