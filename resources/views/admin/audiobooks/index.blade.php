<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>إدارة الكتب الصوتية</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { margin: 0; font-family: 'Cairo', sans-serif; color: #f0f0f0; background: linear-gradient(-45deg, #0b1120, #1c2a4a, #3a506b, #1e3b5c ); background-size: 400% 400%; animation: gradientBG 15s ease infinite; padding: 40px 0; }
        @keyframes gradientBG { 0% {background-position: 0% 50%;} 50% {background-position: 100% 50%;} 100% {background-position: 0% 50%;} }
        .container { max-width: 1400px; margin: auto; background: rgba(255, 255, 255, 0.95); color: #333; padding: 30px 40px; border-radius: 15px; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2); position: relative; }
        .header-container { display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }
        .header-container h1 { color: #1c2a4a; font-size: 2.5rem; margin: 0; }
        .filters-container { display: flex; gap: 15px; margin-bottom: 25px; align-items: flex-end; }
        .filters-container form { display: flex; gap: 15px; width: 100%; }
        .filters-container input, .filters-container select { border: 1px solid #ccc; padding: 10px 15px; font-size: 1rem; font-family: 'Cairo', sans-serif; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .filters-container input { flex-grow: 1; }
        .filters-container button { border: none; background-color: #1c2a4a; color: white; padding: 10px 25px; cursor: pointer; font-size: 1rem; font-weight: bold; transition: background-color 0.3s; border-radius: 8px; }
        .filters-container button:hover { background-color: #3498db; }
        .reset-btn { background-color: #95a5a6; text-decoration: none; display: flex; align-items: center; justify-content: center; }
        .reset-btn:hover { background-color: #7f8c8d; }
        .main-table { width: 100%; border-collapse: collapse; }
        .main-table th, .main-table td { padding: 12px 15px; text-align: right; border-bottom: 1px solid #e0e0e0; vertical-align: middle; }
        .main-table thead th { background-color: #1c2a4a; color: white; font-size: 1rem; text-transform: uppercase; }
        .main-table tbody tr:hover { background-color: #f5f5f5; }
        .cover-image { width: 50px; height: 70px; object-fit: cover; border-radius: 4px; }
        .actions-cell { display: flex; gap: 8px; align-items: center; }
        .action-btn { border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 0.8rem; transition: all 0.2s ease; text-decoration: none; color: white !important; display: inline-block; text-align: center; }
        .btn-delete { background-color: #e74c3c; }
        .btn-view { background-color: #3498db; }
        .action-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; color: white; text-align: center; }
        .status-approved { background-color: #28a745; }
        .status-pending { background-color: #ffc107; color: #333; }
        .status-rejected { background-color: #dc3545; }
        .btn-approve { background-color: #28a745; }
        .btn-reject { background-color: #e74c3c; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 8px; text-align: center; font-weight: bold; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .pagination-container { margin-top: 30px; display: flex; justify-content: center; }
        .btn-back { text-decoration: none; font-size: 1rem; color: #fff; background-color: #34495e; padding: 10px 20px; border-radius: 8px; font-weight: bold; transition: all 0.3s ease; }
        .btn-back:hover { background-color: #2c3e50; }
        .stats-cell { font-size: 0.9rem; white-space: nowrap; }
        .stats-cell .stat-item { display: inline-block; margin-left: 10px; }
        .stat-item .fa-star { color: #f1c40f; }
        .stat-item .fa-flag { color: #e74c3c; }
        .btn-delete[disabled] { background-color: #95a5a6; cursor: not-allowed; }
        .btn-delete[disabled]:hover { transform: none; box-shadow: none; }
        .tooltip-custom { position: relative; display: inline-block; }
        .tooltip-custom .tooltiptext-custom { visibility: hidden; width: 280px; background-color: #555; color: #fff; text-align: center; border-radius: 6px; padding: 8px; position: absolute; z-index: 1; bottom: 125%; left: 50%; margin-left: -140px; opacity: 0; transition: opacity 0.3s; font-size: 0.85rem; }
        .tooltip-custom:hover .tooltiptext-custom { visibility: visible; opacity: 1; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-container"><h1>إدارة الكتب الصوتية</h1></div>
        <div class="filters-container">
            <form action="{{ route('admin.audiobooks.index') }}" method="GET">
                <input type="text" name="search" placeholder="ابحث عن كتاب..." value="{{ $search ?? '' }}">
                <select name="category_id">
                    <option value="">كل الفئات</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ ($selectedCategory ?? '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                <select name="status">
                    <option value="">كل الحالات</option>
                    <option value="pending" {{ ($selectedStatus ?? '') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                    <option value="approved" {{ ($selectedStatus ?? '') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                    <option value="rejected" {{ ($selectedStatus ?? '') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                </select>
                <button type="submit">تطبيق الفلاتر</button>
                <a href="{{ route('admin.audiobooks.index') }}" class="action-btn reset-btn">إعادة تعيين</a>
            </form>
        </div>
        @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if (session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif
        @if (session('warning'))<div class="alert alert-warning">{{ session('warning') }}</div>@endif

        <div style="overflow-x:auto;">
            <table class="main-table">
                <thead>
                    <tr>
                        <th>الغلاف</th><th>عنوان الكتاب</th><th>الناشر</th><th>الفئة</th><th>الإحصائيات</th><th>الحالة</th><th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($audiobooks as $book)
                        <tr>
                            @php
                                $hasImage = $book->cover_image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($book->cover_image_path);
                                $imageUrl = $hasImage ? asset('storage/' . $book->cover_image_path) : 'https://via.placeholder.com/50x70?text=No+Cover';
                            @endphp
                            <td><img src="{{ $imageUrl }}" 
                                     alt="غلاف" 
                                     class="cover-image"
                                     onerror="this.src='https://via.placeholder.com/50x70?text=No+Cover'; this.onerror=null;"></td>
                            <td>{{ $book->title }}</td>
                            <td>{{ $book->publisher->name ?? 'غير محدد' }}</td>
                            <td>{{ $book->category->name ?? 'غير محدد' }}</td>
                            <td class="stats-cell">
                                <span class="stat-item" title="متوسط التقييم"><i class="fas fa-star"></i> {{ number_format($book->ratings_avg_rating ?? 0, 1) }} ({{ $book->ratings_count }})</span>
                                <span class="stat-item" title="عدد الإبلاغات"><i class="fas fa-flag"></i> {{ $book->reports_count }}</span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $book->status }}">
                                    @if($book->status == 'pending') قيد المراجعة @elseif($book->status == 'approved') موافق عليه @elseif($book->status == 'rejected') مرفوض @else {{ $book->status }} @endif
                                </span>
                            </td>
                            <td class="actions-cell">
                                @if($book->status == 'pending')
                                    <button type="button" class="action-btn btn-approve" data-url="{{ route('admin.audiobooks.approve', $book->id) }}" title="الموافقة على الكتاب">موافقة</button>
                                    <button type="button" class="action-btn btn-reject" data-url="{{ route('admin.audiobooks.reject', $book->id) }}" title="رفض الكتاب">رفض</button>
                                @else
                                    @if($book->status == 'approved')
                                        <a href="{{ route('admin.audiobooks.preview', $book->id) }}" class="action-btn btn-view" target="_blank" title="عرض تفاصيل الكتاب">عرض</a>
                                    @endif
                                @endif

                                @php
                                    $averageRating = $book->ratings_avg_rating;
                                    $reportsCount = $book->reports_count;
                                    $canBeDeleted = false;
                                    $reasonsForDeletion = [];

                                    if ($averageRating !== null && $averageRating < 3.0) {
                                        $canBeDeleted = true;
                                        $reasonsForDeletion[] = 'تقييمه أقل من 3';
                                    }
                                    if ($reportsCount > 3) {
                                        $canBeDeleted = true;
                                        $reasonsForDeletion[] = 'إبلاغاته أكثر من 3';
                                    }

                                    if (!$canBeDeleted) {
                                        $tooltipMessage = "لا يمكن الحذف. الشروط: تقييم < 3 (حالياً " . number_format($averageRating ?? 0, 1) . ") أو إبلاغات > 3 (حالياً {$reportsCount}).";
                                    } else {
                                        $tooltipMessage = "يمكن الحذف بسبب: " . implode(' و ', array_unique($reasonsForDeletion));
                                    }
                                @endphp

                                <form action="{{ route('admin.audiobooks.destroy', $book->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الكتاب نهائياً؟');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <div class="tooltip-custom">
                                        <button type="submit" class="action-btn btn-delete" title="{{ $tooltipMessage }}" @if(!$canBeDeleted) disabled @endif>حذف</button>
                                        @if(!$canBeDeleted)
                                            <span class="tooltiptext-custom">{{ $tooltipMessage }}</span>
                                        @endif
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align: center; padding: 30px; font-size: 1.2rem;">لا توجد كتب تطابق معايير البحث.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-container">{!! $audiobooks->appends(request()->query())->links() !!}</div>
        <div style="text-align: center; margin-top: 40px;"><a href="{{ route('admin.dashboard') }}" class="btn-back">العودة إلى لوحة التحكم</a></div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        document.querySelectorAll('.btn-approve, .btn-reject').forEach(button => {
            button.addEventListener('click', function() {
                const url = this.dataset.url;
                const action = this.classList.contains('btn-approve') ? 'الموافقة' : 'الرفض';
                const row = this.closest('tr');

                if (!confirm(`هل أنت متأكد من ${action} على هذا الكتاب؟`)) {
                    return;
                }

                fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        row.querySelector('.actions-cell').innerHTML = 'تم اتخاذ الإجراء';
                        const statusBadge = row.querySelector('.status-badge');
                        if(action === 'الموافقة') {
                            statusBadge.className = 'status-badge status-approved';
                            statusBadge.textContent = 'موافق عليه';
                        } else {
                            statusBadge.className = 'status-badge status-rejected';
                            statusBadge.textContent = 'مرفوض';
                        }
                    } else {
                        alert('فشل الإجراء: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('حدث خطأ في الشبكة.');
                });
            });
        });
    </script>
</body>
</html>
