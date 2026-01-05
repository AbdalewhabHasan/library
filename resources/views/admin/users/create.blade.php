<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- ▼▼▼ تم تعديل العنوان ▼▼▼ --}}
    <title>إنشاء مستخدم جديد</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        
    <style>
        /* نفس الخلفية المتناسقة */
        body {
            margin: 0;
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(-45deg, #0b1120, #1c2a4a, #3a506b, #1e3b5c );
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
        .create-container {
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
            background-color: #28a745; /* لون أخضر مختلف قليلاً للإنشاء */
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
    <div class="create-container">
        {{-- ▼▼▼ تم تعديل العنوان ▼▼▼ --}}
        <h1>إنشاء مستخدم جديد</h1>
{{-- ▼▼▼ هذا هو الكود السحري الذي سيكشف لنا الخطأ ▼▼▼ --}}
@if ($errors->any())
    <div style="background-color: #f8d7da; color: #721c24; padding: 20px; border-radius: 10px; border: 2px solid #f5c6cb; margin-bottom: 30px; font-family: 'Cairo', sans-serif;">
        <h4 style="margin-top: 0; font-weight: bold;">حدث خطأ!</h4>
        <ul style="margin-bottom: 0; padding-right: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
{{-- ▲▲▲ انتهى الكود السحري ▲▲▲ --}}

        {{-- ▼▼▼ تم تعديل الفورم ▼▼▼ --}}
        {{-- ▼▼▼ هذا هو السطر الصحيح الذي يجب استخدامه ▼▼▼ --}}
<form action="{{ route('admin.users.store') }}" method="POST">

            @csrf
            {{-- لا نحتاج @method('PUT') هنا --}}

            <!-- حقل الاسم -->
            <div class="form-group">
                <label for="name">الاسم</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            </div>

            <!-- حقل البريد الإلكتروني -->
            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>

            {{-- ▼▼▼ تم إضافة حقول كلمة المرور ▼▼▼ --}}
            <!-- حقل كلمة المرور -->
            <div class="form-group">
                <label for="password">كلمة المرور</label>
                <input type="password" id="password" name="password" required>
            </div>

            <!-- حقل تأكيد كلمة المرور -->
            <div class="form-group">
                <label for="password_confirmation">تأكيد كلمة المرور</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>
            {{-- ▲▲▲ انتهت الإضافة ▲▲▲ --}}

            <!-- حقل اختيار الدور -->
            <div class="form-group">
                <label for="role">الدور (Role)</label>
                <select id="role" name="role" required>
                    <option value="" disabled selected>-- اختر دوراً --</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>
                            {{ ucfirst($role) }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- أزرار الإنشاء والإلغاء -->
      <div class="form-actions">
    <button type="submit" class="btn btn-save">
        <i class="fas fa-plus-circle"></i> إنشاء المستخدم
    </button>
    
    {{-- ▼▼▼ تم تعديل هذا الزر ▼▼▼ --}}
    <a href="{{ route('admin.dashboard') }}" class="btn btn-cancel">
        <i class="fas fa-tachometer-alt"></i> الرجوع للوحة التحكم
    </a>
    {{-- ▲▲▲ انتهى التعديل ▲▲▲ --}}
</div>

        </form>
    </div>
</body>
</html>
