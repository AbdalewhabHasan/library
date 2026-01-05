<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    {{-- تم تغيير العنوان ليعكس محتوى الصفحة --}}
    <title>تفاصيل الإبلاغ رقم {{ $report->id }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    {{-- تم نسخ نفس الـ CSS من الداشبورد مع إضافات بسيطة --}}
    <style>
        body{margin:0;font-family:'Cairo',sans-serif;color:#f0f0f0;background:linear-gradient(-45deg,#0b1120,#1c2a4a,#3a506b,#1e3b5c );background-size:400% 400%;animation:gradientBG 15s ease infinite}@keyframes gradientBG{0%{background-position:0 50%}50%{background-position:100% 50%}100%{background-position:0 50%}}.sticky-navbar{position:sticky;top:0;left:0;width:100%;background:rgba(11,17,32,.85);backdrop-filter:blur(10px);padding:15px 0;box-shadow:0 4px 15px rgba(0,0,0,.3);z-index:1000;border-bottom:1px solid rgba(255,255,255,.1)}.navbar-container{max-width:1800px;margin:auto;padding:0 40px;display:flex;justify-content:space-between;align-items:center}.navbar-brand{font-size:1.5rem;font-weight:700;color:#ecf0f1}.navbar-actions{display:flex;align-items:center}.navbar-actions .action-button,.navbar-actions form{margin:0 8px}.action-button{display:inline-flex;align-items:center;gap:8px;text-decoration:none;padding:10px 20px;border-radius:8px;font-weight:bold;font-size:.9rem;color:#fff;transition:all .3s ease;border:none;cursor:pointer}.btn-manage-users{background-color:#2980b9}.btn-manage-users:hover{background-color:#3498db}.btn-logout{background-color:#c0392b}.btn-logout:hover{background-color:#e74c3c}.btn-manage-books{background-color:#8e44ad}.btn-manage-books:hover{background-color:#9b59b6}.btn-manage-categories{background-color:#27ae60}.btn-manage-categories:hover{background-color:#2ecc71}.btn-manage-plans{background-color:#d35400}.btn-manage-plans:hover{background-color:#e67e22}.btn-settings{background-color:#f39c12}.btn-settings:hover{background-color:#f1c40f}.main-container{padding:40px;max-width:1800px;margin:auto}.header{text-align:center;margin-bottom:30px}.header h1{font-size:2.5rem;margin:0}.card{background:rgba(255,255,255,.08);border-radius:15px;padding:25px;border:1px solid rgba(255,255,255,.1);display:flex;flex-direction:column; margin-bottom: 25px;}.card-header{margin:-25px -25px 20px -25px;padding:15px 25px;font-size:1.2rem;font-weight:600;color:#ecf0f1;border-bottom:1px solid rgba(255,255,255,.1);display:flex;align-items:center;gap:10px}
        .badge{padding:5px 10px;border-radius:20px;font-size:.8rem;font-weight:bold}.badge-info{background-color:#3498db}.badge-warning{background-color:#f39c12}.badge-secondary{background-color:#95a5a6}.badge-success{background-color:#2ecc71}.badge-danger{background-color:#e74c3c}.btn{border-radius:8px;border:none;padding:10px 15px;cursor:pointer;font-weight:bold;color:white;}.btn-info{background-color:#3498db}.btn-secondary{background-color:#3a506b}.btn-success{background-color:#2ecc71}.btn-danger{background-color:#e74c3c}.blockquote{background:rgba(0,0,0,0.2);padding:15px;border-left:4px solid #3498db;margin:15px 0}.blockquote p{margin:0}.blockquote-footer{color:#bdc3c7;margin-top:10px}
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
                <a href="{{ route('admin.reports.index') }}" class="action-button" style="background-color: #e67e22;"><i class="fas fa-flag"></i> إدارة الإبلاغات</a>
                <a href="{{ route('admin.categories.index') }}" class="action-button btn-manage-categories"><i class="fas fa-tags"></i> إدارة الفئات</a>
                <a href="{{ route('admin.plans.index') }}" class="action-button btn-manage-plans"><i class="fas fa-dollar-sign"></i> إدارة الخطط</a>
                <a href="{{ route('admin.settings.index') }}" class="action-button btn-settings"><i class="fas fa-cog"></i> الإعدادات</a>
                <a href="{{ route('admin.advertisements.index') }}" class="action-button" style="background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);"><i class="fas fa-ad"></i> إدارة الإعلانات</a>
                <a href="#" id="admin-logout-button" class="action-button btn-logout"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
            </div>
        </div>
    </div>
    <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
    {{-- ▲▲▲ نهاية قسم الـ Navbar ▲▲▲ --}}


    {{-- ▼▼▼ هذا هو المحتوى الجديد الخاص بصفحة تفاصيل البلاغ ▼▼▼ --}}
    <div class="main-container">
        <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary mb-4" style="margin-bottom: 25px; display: inline-block;">
            <i class="fas fa-arrow-right"></i> العودة إلى القائمة
        </a>

        <div class="header">
            <h1>تفاصيل الإبلاغ رقم: {{ $report->id }}</h1>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
            {{-- العمود الأيمن (التفاصيل) --}}
            <div>
                <div class="card">
                    <div class="card-header"><i class="fas fa-info-circle"></i> العنصر المُبلغ عنه</div>
                    <div class="card-body">
                        @if ($report->reportable)
                            @if ($report->reportable_type == 'App\Models\AudioBook')
                                <h3>كتاب صوتي: {{ $report->reportable->title }}</h3>
                                <p><strong>الناشر:</strong> {{ $report->reportable->publisher->name ?? 'غير معروف' }}</p>
                                <p style="line-height: 1.8;"><strong>الوصف:</strong> {{ $report->reportable->description }}</p>
                                <a href="{{ route('listener.audiobook.show', $report->reportable->id) }}" target="_blank" class="btn btn-info">
                                    <i class="fas fa-book-open"></i> عرض الكتاب على الموقع
                                </a>
                            @elseif ($report->reportable_type == 'App\Models\Comment')
                                <h3>تعليق على كتاب: {{ $report->reportable->audioBook->title ?? 'كتاب محذوف' }}</h3>
                                <blockquote class="blockquote">
                                    <p class="mb-0">"{{ $report->reportable->comment }}"</p>
                                    <footer class="blockquote-footer">
                                        بواسطة: <cite>{{ $report->reportable->user->name ?? 'مستخدم محذوف' }}</cite>
                                    </footer>
                                </blockquote>
                                @if($report->reportable->audioBook)
                                <a href="{{ route('listener.audiobook.show', $report->reportable->audioBook->id) }}#comments-section" target="_blank" class="btn btn-info">
                                    <i class="fas fa-comments"></i> عرض التعليق في سياقه
                                </a>
                                @endif
                            @endif
                        @else
                            <div style="background-color: #c0392b; padding: 15px; border-radius: 8px;">
                                <strong>عنصر محذوف!</strong> العنصر الذي تم الإبلاغ عنه لم يعد موجوداً في النظام.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><i class="fas fa-exclamation-triangle"></i> سبب الإبلاغ</div>
                    <div class="card-body">
                        <p style="font-size: 1.1rem; line-height: 1.8;">{{ $report->reason }}</p>
                    </div>
                </div>
            </div>

            {{-- العمود الأيسر (الإجراءات) --}}
            <div>
                <div class="card">
                    <div class="card-header"><i class="fas fa-cogs"></i> معلومات وإجراءات</div>
                    <div class="card-body">
                        <p><strong>المُبلغ:</strong> {{ $report->user->name ?? 'مستخدم محذوف' }}</p>
                        <p><strong>تاريخ الإبلاغ:</strong> {{ $report->created_at->format('Y-m-d H:i A') }}</p>
                        <p>
                            <strong>الحالة الحالية:</strong>
                            <span id="current-status-badge">
                                @if ($report->status == 'pending')<span class="badge badge-secondary">قيد المراجعة</span>@elseif ($report->status == 'reviewed')<span class="badge badge-success">تمت المراجعة</span>@elseif ($report->status == 'rejected')<span class="badge badge-danger">مرفوض</span>@endif
                            </span>
                        </p>
                        <hr style="border-color: rgba(255,255,255,0.2);">
                        <h6 style="margin-bottom: 10px;">تغيير الحالة:</h6>
                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-secondary" onclick="updateStatus('pending')">قيد المراجعة</button>
                            <button class="btn btn-success" onclick="updateStatus('reviewed')">تمت المراجعة</button>
                            <button class="btn btn-danger" onclick="updateStatus('rejected')">مرفوض</button>
                        </div>
                        <small id="status-feedback" style="display: block; margin-top: 15px;"></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- ▲▲▲ نهاية المحتوى الجديد ▲▲▲ --}}


    {{-- ▼▼▼ قسم الـ Scripts (مع إضافة كود تحديث الحالة) ▼▼▼ --}}
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

        // كود تحديث حالة البلاغ (AJAX)
        function updateStatus(newStatus) {
            document.getElementById('status-feedback').innerHTML = '<span style="color: #f1c40f;">جاري تحديث الحالة...</span>';
            fetch("{{ route('admin.reports.updateStatus', $report->id) }}", {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let badge;
                    if (newStatus === 'pending') {
                        badge = '<span class="badge badge-secondary">قيد المراجعة</span>';
                    } else if (newStatus === 'reviewed') {
                        badge = '<span class="badge badge-success">تمت المراجعة</span>';
                    } else {
                        badge = '<span class="badge badge-danger">مرفوض</span>';
                    }
                    document.getElementById('current-status-badge').innerHTML = badge;
                    document.getElementById('status-feedback').innerHTML = '<span style="color: #2ecc71;">' + data.message + '</span>';
                } else {
                    document.getElementById('status-feedback').innerHTML = '<span style="color: #e74c3c;">حدث خطأ ما.</span>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('status-feedback').innerHTML = '<span style="color: #e74c3c;">فشل الاتصال بالخادم.</span>';
            });
        }
    </script>

</body>
</html>
