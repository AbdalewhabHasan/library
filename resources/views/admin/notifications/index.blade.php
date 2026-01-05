<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>سجل الإشعارات</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body { margin: 0; font-family: 'Cairo', sans-serif; background: linear-gradient(-45deg, #0b1120, #1c2a4a, #3a506b, #1e3b5c ); background-size: 400% 400%; animation: gradientBG 15s ease infinite; color: #f0f0f0; padding: 40px; }
        @keyframes gradientBG { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        .container { max-width: 1200px; margin: auto; background: rgba(255, 255, 255, 0.08); border-radius: 15px; padding: 30px; border: 1px solid rgba(255, 255, 255, 0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid rgba(255, 255, 255, 0.2); padding-bottom: 20px; }
        .header h1 { margin: 0; font-size: 2rem; }
        .btn { text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; transition: all 0.3s ease; border: none; cursor: pointer; }
        .btn-back { background-color: #3498db; color: white; }
        .notifications-table { width: 100%; border-collapse: collapse; }
        .notifications-table th, .notifications-table td { padding: 15px; text-align: right; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .notifications-table th { font-size: 1.1rem; }
        .notification-item.unread { background-color: rgba(52, 152, 219, 0.1); font-weight: bold; }
        .notification-item a { color: #3498db; text-decoration: none; font-weight: bold; }
        .notification-item .icon { margin-left: 10px; }
        .empty-state { text-align: center; padding: 50px; font-size: 1.2rem; }
        .pagination-container { margin-top: 30px; display: flex; justify-content: center; }
        /* يمكنك نسخ تنسيقات الـ pagination من أي صفحة أخرى لديك */
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-history"></i> سجل الإشعارات</h1>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-back">العودة للوحة التحكم</a>
        </div>
        {{-- ▼▼▼ أضف هذا الكود في أعلى صفحة عرض كل الإشعارات ▼▼▼ --}}

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">كل الإشعارات</h1>

    {{-- زر تمييز الكل كمقروء (الآن على اليسار) --}}
    @if($notifications->where('is_read', false)->count() > 0)
        <form action="{{ route('admin.notifications.markAllAsRead') }}" method="POST">
            @csrf
            {{--
                - btn-success: يعطي الزر اللون الأخضر.
                - text-white: يجعل الأيقونة بيضاء لتتناسب مع الخلفية الخضراء.
                - data-bs-toggle="tooltip": لتفعيل التلميح عند التحويم.
                - title="تمييز الكل كمقروء": هذا هو النص الذي سيظهر عند التحويم.
            --}}
            <button type="submit" class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="تمييز الكل كمقروء">
                <i class="fas fa-check-double"></i>
            </button>
        </form>
    @endif

</div>

{{-- ... باقي كود عرض الإشعارات يبقى كما هو ... --}}


        <table class="notifications-table">
            <thead>
                <tr>
                    <th>الإشعار</th>
                    <th>الوقت</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($notifications as $notification)
                    <tr class="notification-item {{ !$notification->is_read ? 'unread' : '' }}">
                        <td>
                          {{-- ▼▼▼ هذا هو التصحيح الصحيح 100% ▼▼▼ --}}
<a href="{{ $notification->data['link'] ?? '#' }}">
    <i class="{{ $notification->data['icon'] ?? 'fas fa-info-circle' }} icon"></i>
    {{ $notification->data['message'] ?? 'إشعار فارغ' }}
</a>

                        </td>
                        <td>{{ $notification->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="empty-state">
                            <i class="fas fa-check-circle"></i> لا توجد إشعارات لعرضها.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-container">
            {{ $notifications->links() }}
        </div>
    </div>
    @push('scripts')
<script>
    // تفعيل كل التلميحات في الصفحة
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush
</body>
</html>
