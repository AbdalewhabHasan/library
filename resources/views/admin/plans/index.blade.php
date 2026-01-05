<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة الخطط - لوحة تحكم الأدمن</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        
    <style>
        /* === BODY AND BACKGROUND === */
        body {
            margin: 0;
            font-family: 'Cairo', sans-serif;
            color: #f0f0f0;
            background: linear-gradient(-45deg, #0b1120, #1c2a4a, #3a506b, #1e3b5c );
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* === STICKY NAVBAR === */
        .sticky-navbar {
            position: sticky;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(11, 17, 32, 0.85);
            backdrop-filter: blur(10px);
            padding: 15px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .navbar-container {
            max-width: 1400px;
            margin: auto;
            padding: 0 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ecf0f1;
        }
        .navbar-actions .action-button {
            margin: 0 8px;
        }

        /* === MAIN CONTAINER === */
        .container {
            padding: 40px;
            max-width: 1400px;
            margin: auto;
        }

        /* === ACTION BUTTONS (داخل الشريط) === */
        .action-button {
            display: inline-block;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 0.9rem;
            color: white;
            transition: all 0.3s ease;
            border: none;
        }
        .btn-dashboard { background-color: #16a085; }
        .btn-dashboard:hover { background-color: #1abc9c; }
        .btn-manage-users { background-color: #2980b9; }
        .btn-manage-users:hover { background-color: #3498db; }
        .btn-logout { background-color: #c0392b; }
        .btn-logout:hover { background-color: #e74c3c; }
        .btn-manage-books { background-color: #8e44ad; }
        .btn-manage-books:hover { background-color: #9b59b6; }
        .btn-manage-categories { background-color: #27ae60; }
        .btn-manage-categories:hover { background-color: #2ecc71; }
        .btn-manage-plans { background-color: #f39c12; }
        .btn-manage-plans:hover { background-color: #f1c40f; }

        /* === CSS الخاص ببطاقات الخطط === */
        .plan-card {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: all 0.3s ease;
        }
        .plan-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border-color: rgba(243, 156, 18, 0.5);
        }
        .plan-header {
            padding: 25px;
            text-align: center;
            position: relative;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0));
        }
        .plan-name { font-size: 1.8rem; font-weight: 700; color: white; margin: 0; }
        .plan-price { margin-top: 10px; }
        .price-amount { font-size: 2.5rem; font-weight: bold; color: #f39c12; }
        .price-duration { font-size: 1rem; color: #bdc3c7; }
        .plan-status {
            position: absolute;
            top: 15px;
            left: 15px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            color: white;
        }
        .plan-status.active { background-color: rgba(39, 174, 96, 0.8); }
        .plan-status.inactive { background-color: rgba(127, 140, 141, 0.8); }
        .plan-body { padding: 25px; flex-grow: 1; }
        .plan-description { color: #ecf0f1; font-size: 0.95rem; line-height: 1.6; min-height: 70px; }
        .plan-footer { padding: 20px; text-align: center; background: rgba(0, 0, 0, 0.2); }
        .btn-primary-custom { background-color: #3498db; border: none; color: white; }
        .btn-primary-custom:hover { background-color: #5dade2; }
        .btn-danger-custom { background-color: #e74c3c; border: none; color: white; }
        .btn-danger-custom:hover { background-color: #f1948a; }
        .pagination .page-link {
            background-color: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            margin: 0 5px;
            border-radius: 8px;
        }
        .pagination .page-item.active .page-link { background-color: #f39c12; border-color: #f39c12; }
        .pagination .page-item.disabled .page-link { background-color: rgba(0,0,0,0.2); border-color: rgba(255,255,255,0.1); }
    </style>
</head>
<body>

    <div class="sticky-navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <i class="fas fa-user-shield"></i> لوحة تحكم الأدمن
            </div>
            <div class="navbar-actions">
                <a href="{{ route('admin.dashboard') }}" class="action-button btn-dashboard">
                    <i class="fas fa-tachometer-alt"></i> لوحة التحكم
                </a>
                <a href="{{ route('admin.users.index') }}" class="action-button btn-manage-users">
                    <i class="fas fa-users"></i> إدارة المستخدمين
                </a>
                <a href="{{ route('admin.audiobooks.index') }}" class="action-button btn-manage-books">
                    <i class="fas fa-book"></i> إدارة الكتب
                </a>
                <a href="{{ route('admin.categories.index') }}" class="action-button btn-manage-categories">
                    <i class="fas fa-tags"></i> إدارة الفئات
                </a>
                <a href="{{ route('admin.plans.index') }}" class="action-button btn-manage-plans">
                    <i class="fas fa-gem"></i> إدارة الخطط
                </a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="action-button btn-logout">
                        <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="container">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #198754; color: white; border: none;">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 style="color: white; text-shadow: 0 0 10px rgba(255,255,255,0.3);">إدارة خطط الاشتراك</h1>
            <a href="{{ route('admin.plans.create') }}" class="btn btn-lg btn-success" style="box-shadow: 0 0 15px rgba(46, 204, 113, 0.5);">
                <i class="fas fa-plus me-2"></i> إضافة خطة جديدة
            </a>
        </div>

        @if($plans->isEmpty())
            <div class="text-center p-5" style="background: rgba(255, 255, 255, 0.08); border-radius: 15px;">
                <i class="fas fa-box-open fa-4x mb-3" style="color: rgba(255,255,255,0.3);"></i>
                <h3 style="color: white;">لا توجد خطط اشتراك بعد</h3>
                <p class="text-white-50">ابدأ بإضافة خطتك الأولى لجعل منصتك جاهزة للاشتراكات.</p>
            </div>
        @else
            <div class="row g-4">
                @foreach($plans as $plan)
                    <div class="col-lg-4 col-md-6">
                        <div class="plan-card">
                            <div class="plan-header">
                                <div class="plan-status {{ $plan->is_active ? 'active' : 'inactive' }}">
                                    {{ $plan->is_active ? 'نشطة' : 'غير نشطة' }}
                                </div>
                                <h3 class="plan-name">{{ $plan->name }}</h3>
                                <div class="plan-price">
                                    <span class="price-amount">${{ number_format($plan->price, 2) }}</span>
                                    <span class="price-duration">/ {{ $plan->duration_in_days }} يوم</span>
                                </div>
                            </div>
                            <div class="plan-body">
                                <p class="plan-description">{{ $plan->description ?? 'لا يوجد وصف لهذه الخطة.' }}</p>
                            </div>
                     <div class="plan-footer">
    {{-- ▼▼▼ ربط زر التعديل بمسار التعديل ▼▼▼ --}}
    <a href="{{ route('admin.plans.edit', $plan->id) }}" class="btn btn-sm btn-primary-custom"><i class="fas fa-edit me-1"></i> تعديل</a>
    
    {{-- ▼▼▼ ربط نموذج الحذف بمسار الحذف ▼▼▼ --}}
    <form action="{{ route('admin.plans.destroy', $plan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخطة؟ لا يمكن التراجع عن هذا الإجراء!')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger-custom"><i class="fas fa-trash me-1"></i> حذف</button>
    </form>
</div>

                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-5 d-flex justify-content-center">
                {{ $plans->links() }}
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
