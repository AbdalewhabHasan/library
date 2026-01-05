<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة إعلان جديد</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    {{-- ▼▼▼ ربط نفس ملف الـ CSS الاحترافي ▼▼▼ --}}
    <link rel="stylesheet" href="{{ asset('css/admin-styles.css' ) }}">
</head>
<body class="admin-page">
    <div class="admin-container">
        <div class="form-container">
            <h2 style="text-align: center; margin-bottom: 2.5rem; font-weight: 800;">إضافة إعلان جديد</h2>

            @if ($errors->any())
                <div class="alert-admin alert-admin-danger">
                    <ul style="margin: 0; padding-right: 1.5rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.advertisements.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="image" class="form-label"><i class="fas fa-image"></i> صورة الإعلان</label>
                    <input type="file" class="form-control-admin" id="image" name="image" required>
                </div>
                <div class="form-group">
                    <label for="link_url" class="form-label"><i class="fas fa-link"></i> الرابط (URL)</label>
                    <input type="url" class="form-control-admin" id="link_url" name="link_url" placeholder="https://www.example.com" required>
                </div>
                <div class="form-group form-switch-admin">
                    <label for="is_active" class="form-label" style="margin-bottom: 0;">تفعيل الإعلان</label>
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked style="width: 50px; height: 25px; transform: scale(0.8 );">
                </div>
                <div class="form-actions">
                    <a href="{{ route('admin.advertisements.index') }}" class="btn-admin btn-admin-secondary">إلغاء</a>
                    <button type="submit" class="btn-admin btn-admin-primary"><i class="fas fa-save"></i> حفظ الإعلان</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
