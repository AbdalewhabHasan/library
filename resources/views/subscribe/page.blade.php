<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>اختر خطة الاشتراك</title>
    {{-- نفس كود الـ CSS من الصفحات السابقة --}}
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Cairo', sans-serif; color: #f0f0f0; background: linear-gradient(-45deg, #0b1120, #1c2a4a, #3a506b, #1e3b5c ); background-size: 400% 400%; animation: gradientBG 15s ease infinite; }
        @keyframes gradientBG { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        .container { padding: 40px; max-width: 1400px; margin: auto; }
        .plan-card { /* ... نفس تنسيق بطاقات الخطط ... */ }
        /* ... انسخ كل كود الـ CSS الخاص ببطاقات الخطط هنا ... */
    </style>
</head>
<body>
    <div class="container text-center">
        <h1 class="mb-3" style="color: white; text-shadow: 0 0 10px rgba(255,255,255,0.3);">اختر خطة الاشتراك التي تناسبك</h1>
        <p class="lead text-white-50 mb-5">انضم إلى مجتمعنا واستمتع بوصول غير محدود إلى مكتبتنا الصوتية.</p>

        @if($plans->isEmpty())
            <div class="text-center p-5" style="background: rgba(255, 255, 255, 0.08); border-radius: 15px;">
                <h3 style="color: white;">لا توجد خطط متاحة حالياً.</h3>
            </div>
        @else
            <form action="{{ route('subscribe.process') }}" method="POST">
                @csrf
                <div class="row g-4 justify-content-center">
                    @foreach($plans as $plan)
                        <div class="col-lg-4 col-md-6">
                            <label class="plan-card-label">
                                <input type="radio" name="plan_id" value="{{ $plan->id }}" class="d-none">
                            <div class="plan-card">
    {{-- ▼▼▼ هذا هو الكود المحدث لعرض البيانات الفعلية ▼▼▼ --}}
    <div class="plan-header">
        <h3 class="plan-name">{{ $plan->name }}</h3>
        <div class="plan-price">
            <span class="price-amount">${{ number_format($plan->price, 2) }}</span>
            <span class="price-duration">/ {{ $plan->duration_in_days }} يوم</span>
        </div>
    </div>
    <div class="plan-body">
        <p class="plan-description">{{ $plan->description }}</p>
    </div>
    {{-- ▲▲▲ انتهى الكود المحدث ▲▲▲ --}}
    <div class="plan-footer-select">
        <i class="fas fa-check-circle"></i> اختر هذه الخطة
    </div>
</div>

                            </label>
                        </div>
                    @endforeach
                </div>
                <button type="submit" class="btn btn-lg mt-5" style="background-color: #f39c12; color: white; padding: 15px 50px; font-size: 1.5rem;">
                    الاشتراك الآن
                </button>
            </form>
        @endif
    </div>

    <style>
        /* ... انسخ كل كود الـ CSS الخاص ببطاقات الخطط هنا ... */
        .plan-card { background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; overflow: hidden; display: flex; flex-direction: column; height: 100%; transition: all 0.3s ease; cursor: pointer; }
        .plan-card:hover { transform: translateY(-10px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); border-color: rgba(243, 156, 18, 0.5); }
        .plan-header { padding: 25px; text-align: center; position: relative; background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0)); }
        .plan-name { font-size: 1.8rem; font-weight: 700; color: white; margin: 0; }
        .plan-price { margin-top: 10px; }
        .price-amount { font-size: 2.5rem; font-weight: bold; color: #f39c12; }
        .price-duration { font-size: 1rem; color: #bdc3c7; }
        .plan-body { padding: 25px; flex-grow: 1; }
        .plan-description { color: #ecf0f1; font-size: 0.95rem; line-height: 1.6; min-height: 70px; }
        .plan-footer-select { padding: 20px; text-align: center; background: rgba(0, 0, 0, 0.2); font-weight: bold; font-size: 1.2rem; transition: all 0.3s ease; }
        input[type="radio"]:checked + .plan-card {
            border-color: #f39c12;
            box-shadow: 0 0 25px rgba(243, 156, 18, 0.7);
        }
        input[type="radio"]:checked + .plan-card .plan-footer-select {
            background-color: #f39c12;
            color: white;
        }
    </style>
</body>
</html>
