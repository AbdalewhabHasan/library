<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة الإعلانات</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    {{-- ▼▼▼ ربط ملف الـ CSS الاحترافي ▼▼▼ --}}
    <link rel="stylesheet" href="{{ asset('css/admin-styles.css' ) }}">
</head>
<body class="admin-page">
    <div class="admin-container">
        <header class="admin-header">
            <h1><i class="fas fa-ad" style="color: var(--admin-primary);"></i> إدارة الإعلانات</h1>
            <div>
                <a href="{{ route('admin.advertisements.create') }}" class="btn-admin btn-admin-primary"><i class="fas fa-plus"></i> إضافة إعلان</a>
                <a href="{{ route('admin.dashboard') }}" class="btn-admin btn-admin-secondary">العودة للوحة التحكم</a>
            </div>
        </header>

        @if (session('success'))
            <div class="alert-admin alert-admin-success">{{ session('success') }}</div>
        @endif

        <div class="admin-grid">
            @forelse ($advertisements as $ad)
                <div class="admin-card">
                    <img src="{{ asset('storage/' . $ad->image_path) }}" class="admin-card-image" alt="صورة الإعلان">
                    <div class="admin-card-body">
                        <p class="admin-card-link">{{ $ad->link_url }}</p>
                        <div class="admin-card-footer">
                            @if ($ad->is_active)
                                <span class="badge-admin badge-admin-success">فعال</span>
                            @else
                                <span class="badge-admin badge-admin-secondary">غير فعال</span>
                            @endif
                            <form action="{{ route('admin.advertisements.destroy', $ad->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإعلان؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete-admin" title="حذف الإعلان"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; background: var(--admin-bg-card); padding: 3rem; border-radius: var(--admin-radius);">
                    <h2>لا توجد إعلانات حالياً</h2>
                    <p style="color: var(--admin-text-secondary); margin-bottom: 2rem;">ابدأ بإضافة أول إعلان لمنصتك.</p>
                    <a href="{{ route('admin.advertisements.create') }}" class="btn-admin btn-admin-primary"><i class="fas fa-plus"></i> إضافة إعلان الآن</a>
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>
