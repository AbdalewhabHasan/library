<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المستخدمين</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    {{-- ▼▼▼ إضافة أيقونات Font Awesome (إذا لم تكن موجودة بالفعل في layout ) ▼▼▼ --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* === BODY AND BACKGROUND === */
        body {
            margin: 0;
            font-family: 'Cairo', sans-serif;
            color: #f0f0f0;
            background: linear-gradient(-45deg, #0b1120, #1c2a4a, #3a506b, #1e3b5c );
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            padding-top: 40px;
            padding-bottom: 40px;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* === CONTAINER === */
        .container {
            max-width: 1300px; /* زيادة عرض الحاوية قليلاً لتناسب الأعمدة الجديدة */
            margin: auto;
            background: rgba(255, 255, 255, 0.98);
            color: #333;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
        }

        /* === HEADER & BACK BUTTON === */
        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between; /* تغيير لتوزيع العناصر */
            margin-bottom: 20px;
        }
        .header-container h1 {
            margin: 0;
            color: #1c2a4a;
            font-size: 2.5rem;
        }
        .btn-back {
            text-decoration: none;
            font-size: 1.2rem;
            color: white;
            background-color: #34495e;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-back:hover {
            background-color: #2c3e50;
            transform: translateY(-2px);
        }

        /* === FILTERS & SEARCH === */
        .controls-container {
            background-color: #8fbfee;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: center;
        }
        .search-container { flex-grow: 1; min-width: 300px; }
        .search-container form { display: flex; }
        .search-container input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d3e8ee;
            border-radius: 8px 0 0 8px;
            font-size: 1rem;
            font-family: 'Cairo', sans-serif;
            border-left: none;
        }
        .btn-search {
            padding: 12px 25px;
            border: none;
            border-radius: 0 8px 8px 0;
            font-weight: bold;
            cursor: pointer;
            background-color: #34495e;
            color: white;
        }
        .filter-group { display: flex; align-items: center; gap: 10px; }
        .filter-group .filter-label { font-weight: bold; color: #555; }
        .btn-filter {
            text-decoration: none;
            padding: 8px 18px;
            border-radius: 20px;
            font-weight: bold;
            color: #555;
            background-color: #aaccf0;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }
        .btn-filter:hover { background-color: #ced4da; }
        .btn-filter.active {
            background-color: #1c2a4a;
            color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* === PROFESSIONAL TABLE STYLE === */
        .users-table { width: 100%; border-collapse: collapse; }
        .users-table th, .users-table td { padding: 15px; text-align: right; border-bottom: 1px solid #120450; vertical-align: middle; }
        .users-table thead th { background-color: #1c2a4a; color: white; font-size: 1rem; }
        .users-table tbody tr:hover { background-color: #120224; }
        
        /* ▼▼▼ كلاس جديد لتلوين صف المستخدم المحظور ▼▼▼ */
        .users-table tbody tr.banned-user {
            background-color: #2da2e6;
            opacity: 0.7;
        }
        .users-table tbody tr.banned-user:hover {
            opacity: 1;
        }

        /* === ROLE & STATUS BADGES === */
        .badge { padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; color: white; }
        .role-admin { background: #ffc107; }
        .role-publisher { background: #17a2b8; }
        .role-listener { background: #28a745; }
        .status-active { background: #28a745; }
        .status-banned { background: #dc3545; }

        /* === ACTION BUTTONS === */
        .action-btn {
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 0.8rem;
            transition: all 0.2s ease;
            margin: 0 3px;
            color: white;
        }
        .action-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .btn-edit { background-color: #3498db; }
        .btn-delete { background-color: #c0392b; }
        /* ▼▼▼ تنسيق زر الحظر والتفعيل الجديد ▼▼▼ */
        .btn-ban { background-color: #e67e22; }
        .btn-unban { background-color: #2ecc71; }
        
        /* === ALERTS & PAGINATION === */
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 8px; text-align: center; font-weight: bold; }
        .alert-success { background-color: #4fa9e6; color: #155724; }
        .alert-error { background-color: #3f8bfd; color: #721c24; }
        .pagination-container { margin-top: 30px; display: flex; justify-content: center; }
        /* ▼▼▼ أضف هذا التنسيق الجديد في نهاية قسم <style> ▼▼▼ */
.btn-create {
    background-color: #3498db;
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: background-color 0.3s ease;
}
.btn-create:hover {
    background-color: #2980b9;
}
/* ▼▼▼ أضف هذا التنسيق الجديد في نهاية قسم <style> ▼▼▼ */
.btn-back {
    background-color: #f39c12; /* لون برتقالي مميز */
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: background-color 0.3s ease;
    margin-right: 10px; /* لإضافة مسافة بين الزرين */
}
.btn-back:hover {
    background-color: #e67e22;
}


    </style>
</head>
<body>
    {{-- ▼▼▼ هذا هو الجزء الذي ستستبدله ▼▼▼ --}}
<div class="header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h1 style="margin: 0;">إدارة المستخدمين</h1>
    
    {{-- ▼▼▼ هذا هو الجزء المضاف ▼▼▼ --}}
    <div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-create">
            <i class="fas fa-plus"></i> إنشاء مستخدم جديد
        </a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-back">
            <i class="fas fa-arrow-left"></i> الرجوع للوحة التحكم
        </a>
    </div>
    {{-- ▲▲▲ انتهى الجزء المضاف ▲▲▲ --}}
</div>

{{-- ▲▲▲ انتهى الجزء المستبدل ▲▲▲ --}}


        {{-- رسائل النجاح والخطأ --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        {{-- ▼▼▼ حاوية الفلاتر والبحث الجديدة ▼▼▼ --}}
        <div class="controls-container">
            <!-- Search Form -->
            <div class="search-container">
                <form action="{{ route('admin.users.index') }}" method="GET">
                    {{-- حقول مخفية للحفاظ على الفلاتر الأخرى عند البحث --}}
                    @if(request('role')) <input type="hidden" name="role" value="{{ request('role') }}"> @endif
                    @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                    
                    <input type="text" name="search" placeholder="ابحث بالاسم أو البريد الإلكتروني..." value="{{ request('search') }}">
                    <button type="submit" class="btn-search"><i class="fas fa-search"></i></button>
                </form>
            </div>
            
            <!-- Role Filter Buttons -->
            <div class="filter-group">
                <span class="filter-label">الدور:</span>
                <a href="{{ route('admin.users.index', array_merge(request()->except('role'), ['role' => null])) }}" class="btn-filter {{ !request('role') ? 'active' : '' }}">الكل</a>
                <a href="{{ route('admin.users.index', array_merge(request()->except('role'), ['role' => 'publisher'])) }}" class="btn-filter {{ request('role') == 'publisher' ? 'active' : '' }}">الناشرون</a>
                <a href="{{ route('admin.users.index', array_merge(request()->except('role'), ['role' => 'listener'])) }}" class="btn-filter {{ request('role') == 'listener' ? 'active' : '' }}">المستمعون</a>
            </div>

            <!-- Status Filter Buttons -->
            <div class="filter-group">
                <span class="filter-label">الحالة:</span>
                <a href="{{ route('admin.users.index', array_merge(request()->except('status'), ['status' => null])) }}" class="btn-filter {{ !request('status') ? 'active' : '' }}">الكل</a>
                <a href="{{ route('admin.users.index', array_merge(request()->except('status'), ['status' => 'active'])) }}" class="btn-filter {{ request('status') == 'active' ? 'active' : '' }}">النشطون</a>
                <a href="{{ route('admin.users.index', array_merge(request()->except('status'), ['status' => 'banned'])) }}" class="btn-filter {{ request('status') == 'banned' ? 'active' : '' }}">المحظورون</a>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الدور</th>
                        <th>الحالة</th> {{-- ▼ عمود جديد ▼ --}}
                        <th>تاريخ التسجيل</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        {{-- ▼ إضافة كلاس 'banned-user' للصف إذا كان المستخدم محظوراً ▼ --}}
                        <tr class="{{ $user->status === 'banned' ? 'banned-user' : '' }}">
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge role-{{ strtolower($user->role) }}">{{ $user->role }}</span>
                            </td>
                            <td>
                                {{-- ▼ عرض حالة المستخدم بشكل مرئي ▼ --}}
                                @if($user->status === 'active')
                                    <span class="badge status-active">نشط</span>
                                @else
                                    <span class="badge status-banned">محظور</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('Y-m-d') }}</td>
                            <td>
                                {{-- ▼ زر الحظر/التفعيل الجديد ▼ --}}
                                <form action="{{ route('admin.users.toggleStatus', $user->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    @if($user->status === 'active')
                                        <button type="submit" class="action-btn btn-ban" title="حظر هذا المستخدم">
                                            <i class="fas fa-user-slash"></i> حظر
                                        </button>
                                    @else
                                        <button type="submit" class="action-btn btn-unban" title="إلغاء حظر هذا المستخدم">
                                            <i class="fas fa-user-check"></i> تفعيل
                                        </button>
                                    @endif
                                </form>

                                <a href="{{ route('admin.users.edit', $user->id) }}" class="action-btn btn-edit">
                                    <i class="fas fa-pen"></i> تعديل
                                </a>
                                
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد أنك تريد حذف هذا المستخدم نهائياً؟');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn btn-delete">
                                        <i class="fas fa-trash"></i> حذف
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 30px; font-size: 1.2rem;">لا يوجد مستخدمين لعرضهم حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-container">
            {{-- للتأكد من أن الفلاتر والبحث تعمل مع التنقل بين الصفحات --}}
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
</body>
</html>
