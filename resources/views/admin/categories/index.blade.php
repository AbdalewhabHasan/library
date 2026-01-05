<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الفئات</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            margin: 0; font-family: 'Cairo', sans-serif; color: #333;
            background-color: #f4f7f6; padding: 40px 0;
        }
        .container { max-width: 900px; margin: auto; padding: 0 20px; }
        .header-container { text-align: center; margin-bottom: 30px; }
        .header-container h1 { color: #1c2a4a; font-size: 2.5rem; margin: 0; }

        /* --- Form & Table Card --- */
        .card {
            background: #fff; border-radius: 15px; padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08 ); margin-bottom: 30px;
        }
        .card-header {
            font-size: 1.5rem; font-weight: 700; color: #1c2a4a;
            margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eee;
        }

        /* --- Form --- */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #555; }
        .form-group input {
            width: 100%; padding: 12px 15px; font-size: 1rem; font-family: 'Cairo', sans-serif;
            border: 1px solid #ccc; border-radius: 8px; box-sizing: border-box;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-group input:focus {
            outline: none; border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        .btn-submit {
            border: none; background-color: #1c2a4a; color: white; padding: 12px 30px;
            font-size: 1rem; font-weight: bold; cursor: pointer; border-radius: 8px;
            transition: background-color 0.3s; display: block; width: 100%;
        }
        .btn-submit:hover { background-color: #27ae60; }

        /* --- Table --- */
        .main-table { width: 100%; border-collapse: collapse; }
        .main-table th, .main-table td {
            padding: 15px; text-align: right; border-bottom: 1px solid #e0e0e0;
            vertical-align: middle;
        }
        .main-table thead th {
            background-color: #f8f9fa; color: #333; font-size: 1rem;
            font-weight: 700; text-transform: uppercase;
        }
        .main-table tbody tr:hover { background-color: #f5f5f5; }

        /* --- Action Buttons --- */
        .actions-cell { display: flex; gap: 10px; }
        .action-btn {
            border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer;
            font-weight: bold; font-size: 0.85rem; transition: all 0.2s ease;
            text-decoration: none; color: white !important; display: inline-block;
        }
        .btn-edit { background-color: #3498db; }
        .btn-delete { background-color: #e74c3c; }
        .action-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }

        /* --- Alerts --- */
        .alert {
            padding: 15px; margin-bottom: 20px; border-radius: 8px;
            text-align: center; font-weight: bold; border: 1px solid transparent;
        }
        .alert-success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        .error-message { color: #e74c3c; font-size: 0.9rem; margin-top: 5px; }

        /* --- Back Button --- */
        .btn-back {
            text-decoration: none; font-size: 1rem; color: #fff; background-color: #34495e;
            padding: 10px 20px; border-radius: 8px; font-weight: bold;
            transition: all 0.3s ease; display: inline-block;
        }
        .btn-back:hover { background-color: #2c3e50; }
        .pagination-container { margin-top: 30px; display: flex; justify-content: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-container">
            <h1>إدارة الفئات</h1>
        </div>

        {{-- عرض رسائل النجاح أو الخطأ --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- نموذج إضافة فئة جديدة -->
        <div class="card">
            <div class="card-header">إضافة فئة جديدة</div>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">اسم الفئة</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn-submit">حفظ الفئة</button>
            </form>
        </div>

        <!-- جدول عرض الفئات الموجودة -->
        <div class="card">
            <div class="card-header">قائمة الفئات</div>
            <div style="overflow-x:auto;">
                <table class="main-table">
                    <thead>
                        <tr>
                            <th>اسم الفئة</th>
                            <th>تاريخ الإنشاء</th>
                            <th style="width: 150px;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->created_at->format('Y-m-d') }}</td>
                                <td class="actions-cell">
                                    {{-- ▼▼▼ هذا هو التعديل ▼▼▼ --}}
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="action-btn btn-edit">تعديل</a>
                                    
                                   <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الفئة؟');">
    @csrf
    @method('DELETE')
    <button type="submit" class="action-btn btn-delete">حذف</button>
</form>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 30px; font-size: 1.2rem;">
                                    لا توجد فئات حتى الآن.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination-container">
                {{ $categories->links() }}
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="{{ route('admin.dashboard') }}" class="btn-back">العودة إلى لوحة التحكم</a>
        </div>
    </div>
</body>
</html>
