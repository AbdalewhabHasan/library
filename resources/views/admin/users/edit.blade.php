<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل المستخدم: {{ $user->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
        
    <style>
        /* نفس الخلفية المتناسقة */
        body {
            margin: 0;
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(-45deg, #0b1120, #1c2a4a, #3a506b, #1e3b5c);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 40px 0;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* تصميم نموذج التعديل */
        .edit-container {
            width: 100%;
            max-width: 600px;
            background: white;
            color: #333;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #1c2a4a;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: #555;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            font-family: 'Cairo', sans-serif;
            box-sizing: border-box; /* لضمان أن padding لا يزيد العرض */
        }

        .form-actions {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-save {
            background-color: #2ecc71;
            color: white;
        }
        .btn-cancel {
            background-color: #7f8c8d;
            color: white;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <h1>تعديل بيانات المستخدم</h1>

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- حقل الاسم -->
            <div class="form-group">
                <label for="name">الاسم</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
            </div>

            <!-- حقل البريد الإلكتروني -->
            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
            </div>

            <!-- حقل تغيير الدور -->
            <div class="form-group">
                <label for="role">الدور (Role)</label>
                <select id="role" name="role" required>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" {{ $user->role == $role ? 'selected' : '' }}>
                            {{ ucfirst($role) }}
                        </option>
                    @endforeach
                </select>
            </div><!-- أزرار الحفظ والإلغاء -->
            <div class="form-actions">
                <button type="submit" class="btn btn-save">حفظ التغييرات</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-cancel">إلغاء</a>
            </div>
        </form>
    </div>
</body>
</html>