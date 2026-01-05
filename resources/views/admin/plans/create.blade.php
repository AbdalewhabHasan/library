<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة خطة جديدة - لوحة تحكم الأدمن</title>
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

        /* === FORM STYLES === */
        .form-control, .form-select {
            background-color: rgba(0,0,0,0.3);
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            background-color: rgba(0,0,0,0.4);
            color: white;
            border-color: #f39c12;
            box-shadow: 0 0 10px rgba(243, 156, 18, 0.5);
        }
        .form-label {
            color: #ecf0f1;
            font-weight: 600;
        }
        .form-check-input {
            background-color: rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .form-check-input:checked {
            background-color: #f39c12;
            border-color: #f39c12;
        }
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
        <div class="card mx-auto" style="max-width: 800px; background: rgba(255, 255, 255, 0.08); border-radius: 15px; padding: 35px; border: 1px solid rgba(255, 255, 255, 0.1);">
            <div class="card-header bg-transparent" style="border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding-bottom: 15px; margin-bottom: 30px;">
                <h2 style="margin: 0; color: white; text-align: center;"><i class="fas fa-plus-circle me-2"></i> إضافة خطة جديدة</h2>
            </div>
            <div class="card-body p-0">
                @if ($errors->any())
                    <div class="alert alert-danger" style="background-color: rgba(231, 76, 60, 0.8); border: none; color: white;">
                        <h5 class="alert-heading">هناك بعض الأخطاء!</h5>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.plans.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">اسم الخطة</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="مثال: الخطة الذهبية الشهرية" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">السعر ($)</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ old('price') }}" placeholder="مثال: 9.99" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="duration_in_days" class="form-label">المدة (بالأيام)</label>
                            <input type="number" class="form-control" id="duration_in_days" name="duration_in_days" value="{{ old('duration_in_days') }}" placeholder="مثال: 30 (لخطة شهرية)" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">الوصف (اختياري)</label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="اكتب هنا الميزات الرئيسية لهذه الخطة...">{{ old('description') }}</textarea>
                    </div>
                    <div class="form-check form-switch fs-5 mb-4">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">
                            تفعيل الخطة (ستكون متاحة للمستخدمين فوراً)
                        </label>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('admin.plans.index') }}" class="btn btn-secondary btn-lg">إلغاء</a>
                        <button type="submit" class="btn btn-lg" style="background-color: #f39c12; color: white; border: none;"><i class="fas fa-save me-2"></i> حفظ الخطة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
