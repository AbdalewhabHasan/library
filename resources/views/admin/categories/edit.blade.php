<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل الفئة</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            margin: 0; font-family: 'Cairo', sans-serif; color: #333;
            background-color: #f4f7f6; padding: 40px 0;
        }
        .container { max-width: 700px; margin: auto; padding: 0 20px; }
        .header-container { text-align: center; margin-bottom: 30px; }
        .header-container h1 { color: #1c2a4a; font-size: 2.5rem; margin: 0; }

        .card {
            background: #fff; border-radius: 15px; padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08 );
        }
        .card-header {
            font-size: 1.5rem; font-weight: 700; color: #1c2a4a;
            margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eee;
        }

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
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        .btn {
            border: none; padding: 12px 30px; font-size: 1rem; font-weight: bold;
            cursor: pointer; border-radius: 8px; transition: background-color 0.3s;
            text-decoration: none; text-align: center; flex-grow: 1;
        }
        .btn-submit { background-color: #27ae60; color: white; }
        .btn-submit:hover { background-color: #229954; }
        .btn-cancel { background-color: #95a5a6; color: white; }
        .btn-cancel:hover { background-color: #7f8c8d; }

        .error-message { color: #e74c3c; font-size: 0.9rem; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-container">
            <h1>تعديل الفئة</h1>
        </div>

        <div class="card">
            <div class="card-header">تعديل: {{ $category->name }}</div>
            
            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT') {{-- <-- مهم جداً لعملية التحديث --}}
                
                <div class="form-group">
                    <label for="name">الاسم الجديد للفئة</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-submit">حفظ التعديلات</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-cancel">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
