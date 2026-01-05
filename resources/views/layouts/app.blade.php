

 <!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ settings('site_name', config('app.name', 'Laravel')) }}</title>
    <meta name="description" content="{{ settings('site_description', 'استمع إلى آلاف الكتب الصوتية في مختلف المجالات.') }}">

    <!-- Fonts & Icons -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    {{-- مكتبة SweetAlert2 لإشعارات أجمل --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>

     html, body {
        background: linear-gradient(270deg, #ff6b6b, #4ecdc4, #764ba2, #667eea );
        background-size: 800% 800%; /* حجم أكبر لتأثير أكثر سلاسة */
        animation: gradientShift 15s ease infinite; /* مدة أطول للانتقال وتكرار لا نهائي */
        min-height: 100vh;
        margin: 0;
        padding: 0;
        font-family: 'Poppins', sans-serif; /* خط حديث (رح نحتاج نربطه لاحقاً) */
        color: #2c3e50; /* لون نص أساسي داكن */
        overflow-x: hidden; /* منع شريط التمرير الأفقي */
    }

    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* 2. تحسين تصميم الحاويات والظلال */
    .container {
        background: rgba(255, 255, 255, 0.95); /* شفافية أعلى وشفافية لطيفة للخلفية */
        border-radius: 20px; /* حواف أكثر دائرية */
        padding: 35px; /* مساحة داخلية أكبر */
        margin-top: 40px; /* مسافة من الأعلى */
        margin-bottom: 40px; /* مسافة من الأسفل */
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.25), 0 5px 15px rgba(0, 0, 0, 0.15); /* ظلال متعددة لعمق أكبر */
        -webkit-backdrop-filter: blur(5px); /* تأثير ضبابي خفيف للخلفية ورا الحاوي */
        backdrop-filter: blur(5px); /* لدعم متصفحات ويب كيت */
        transform: translateY(0); /* للتحضير لتأثير الدخول */
        opacity: 0; /* للتحضير لتأثير الدخول */
        animation: fadeInSlideUp 1s ease-out forwards; /* تأثير ظهور سلس */
    }

    @keyframes fadeInSlideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* 3. تصميم العناوين */
    h2 {
        color: #007bff; /* لون أزرق مميز */
        text-align: center;
        font-weight: 700; /* خط سميك */
        margin-bottom: 30px; /* مسافة أكبر أسفل العنوان */
        letter-spacing: 1px; /* تباعد أحرف خفيف */
        text-shadow: 1px 1px 2px rgba(0,0,0,0.05); /* ظل نص خفيف */
    }

    /* 4. تصميم نموذج البحث */
    form.mb-4 {
        display: flex;
        gap: 10px; /* مسافة بين حقل البحث والزر */
        margin-bottom: 30px;
        background: rgba(255, 255, 255, 0.8);
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .form-control {
        flex-grow: 1; /* حقل البحث يأخذ أكبر مساحة ممكنة */
        border: 1px solid #ced4da;
        border-radius: 8px;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        outline: none;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
        transform: translateY(-2px); /* تأثير رفع بسيط عند التحويم */
        box-shadow: 0 6px 15px rgba(0, 123, 255, 0.4);
    }

    /* 5. تصميم البطاقات (Cards) بدلاً من الجدول */
    .audio-books-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); /* 280px كحد أدنى لكل بطاقة */
        gap: 25px; /* المسافة بين البطاقات */
        padding: 15px; /* مساحة داخلية للشبكة */
        background: rgba(255, 255, 255, 0.8); /* خلفية خفيفة للشبكة */
        border-radius: 15px;
        box-shadow: inset 0 0 15px rgba(0,0,0,0.05); /* ظل داخلي خفيف */
    }

    .audio-book-card {
        background: #ffffff;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column; /* ترتيب المحتوى عمودياً */
    }

    .audio-book-card:hover {
        transform: translateY(-8px); /* رفع البطاقة عند التحويم */
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2); /* ظل أكبر عند التحويم */
    }

    .card-image-container {
        width: 100%;
        height: 200px; /* ارتفاع ثابت لصورة الغلاف */
        overflow: hidden;
        border-bottom: 1px solid #eee;
    }

    .card-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* لضمان تغطية الصورة للمساحة دون تشويه */
        transition: transform 0.3s ease;
    }
    .audio-book-card:hover .card-image-container img {
        transform: scale(1.05); /* تكبير بسيط للصورة عند التحويم */
    }

    .card-content {
        padding: 20px;
        flex-grow: 1; /* المحتوى يأخذ المساحة المتبقية */
        display: flex;
        flex-direction: column;
    }

    .card-content h3 {
        font-size: 1.4rem;
        font-weight: 700;
        color: #34495e;
        margin-bottom: 10px;
        line-height: 1.3;
    }

    .card-content p {
        font-size: 0.95rem;
        color: #7f8c8d;
        margin-bottom: 8px;
        line-height: 1.5;
    }

    .card-content .info-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
        color: #555;
        margin-top: 5px;
    }

    .card-content .info-item {
        background: #e9ecef;
        padding: 4px 8px;
        border-radius: 5px;
        margin-right: 5px;
        white-space: nowrap; /* منع انقسام النص */
    }

    .card-buttons {
        margin-top: 20px;
        display: flex;
        gap: 10px;
    }

    .card-buttons .btn {
        flex-grow: 1; /* الأزرار تأخذ مساحة متساوية */
        padding: 10px 15px;
        font-size: 0.9rem;
        font-weight: 600;
        text-align: center;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-download {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
        box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);
    }

    .btn-download:hover {
        background-color: #218838;
        border-color: #1e7e34;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(40, 167, 69, 0.4);
    }

    /* 6. رسالة عدم توفر الكتب */
    p {
        text-align: center;
        font-size: 1.2rem;
        color: #555;
        margin-top: 30px;
    }

    /* 7. استجابة التصميم (Responsive Design) */
    @media (max-width: 768px) {
        .container {
            padding: 20px;
            margin-top: 20px;
            margin-bottom: 20px;
            border-radius: 15px;
        }
        .audio-books-grid {
            grid-template-columns: 1fr; /* عمود واحد على الشاشات الصغيرة */
            gap: 20px;
        }
        .form-control, .btn-primary {
            padding: 8px 12px;
            font-size: 0.9rem;
        }
        .card-content h3 {
            font-size: 1.2rem;
        }
        .card-buttons {
            flex-direction: column; /* الأزرار تكون عمودية على الشاشات الصغيرة */
        }
    }
    /* تنسيقات مقترحة لزر العودة */
.btn-outline-primary {
    border-width: 2px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.float-end {
    float: right;
}


        /* ▼▼▼ تنسيقات جديدة خاصة بنافذة الإبلاغ ▼▼▼ */
        .modal-content {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .modal-header {
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        .modal-header .modal-title {
            font-weight: 700;
            color: #34495e;
        }
        .modal-footer {
            border-top: 1px solid rgba(0,0,0,0.1);
        }
    </style>


    {{-- Vite Assets --}}
    @vite(['resources/sass/app.scss', 'resources/js/app.js'] )

    {{-- أي ملفات CSS إضافية --}}
    <link rel="stylesheet" href="{{ asset('css/audiobooks-styles.css' ) }}">


</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            {{-- ... كود شريط التنقل (Navbar) يبقى كما هو ... --}}
             <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                @if(settings('site_logo'))
                    <img src="{{ asset('storage/' . settings('site_logo')) }}" alt="{{ settings('site_name') }}" style="height: 30px; width: auto; margin-left: 10px;">
                @endif
                {{ settings('site_name', config('app.name', 'Laravel')) }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto"></ul>
                <ul class="navbar-nav ms-auto">
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
                        @endif
                        @if (Route::has('register'))
                            <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a></li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    {{-- النافذة المنبثقة الموحدة لنظام الإبلاغات --}}
    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="reportForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reportModalLabel">إبلاغ عن محتوى</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="reportableType" name="reportable_type">
                        <input type="hidden" id="reportableId" name="reportable_id">
                        <div class="mb-3">
                            <label for="reportReason" class="form-label">سبب الإبلاغ (مطلوب):</label>
                            <textarea class="form-control" id="reportReason" name="reason" rows="5" required minlength="10" maxlength="500" placeholder="الرجاء تقديم وصف واضح للمشكلة..."></textarea>
                            <div class="form-text">سيتم مراجعة بلاغك من قبل الإدارة. (10-500 حرف)</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger" id="submitReportBtn">
                            <i class="fas fa-paper-plane"></i> إرسال الإبلاغ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ملفات JavaScript الأساسية --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="{{ asset('mo/audiobooks-scripts.js'   ) }}"></script>

    {{-- ▼▼▼ كود JavaScript المركزي والنهائي لنظام الإبلاغات ▼▼▼ --}}
    @stack('scripts')
    <script>
        // هذه الدالة أصبحت الآن متاحة في كل الصفحات
        function openGlobalReportModal(type, id, name) {
            const reportModal = new bootstrap.Modal(document.getElementById('reportModal'));
            const reportModalLabel = document.getElementById('reportModalLabel');
            const reportableTypeInput = document.getElementById('reportableType');
            const reportableIdInput = document.getElementById('reportableId');

            const typeText = type.includes('AudioBook') ? 'كتاب' : 'تعليق';

            reportModalLabel.textContent = `إبلاغ عن ${typeText}: "${name}"`;
            reportableTypeInput.value = type;
            reportableIdInput.value = id;

            reportModal.show();
        }

        document.addEventListener('DOMContentLoaded', function () {
            const reportForm = document.getElementById('reportForm');
            if (reportForm) {
                reportForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const submitBtn = document.getElementById('submitReportBtn');
                    const submitBtnOriginalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> جاري الإرسال...`;

                    fetch("{{ route('listener.reports.store') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            reportable_type: document.getElementById('reportableType').value,
                            reportable_id: document.getElementById('reportableId').value,
                            reason: document.getElementById('reportReason').value
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('reportModal'));
                        modal.hide();
                        if (data.success) {
                            Swal.fire({ icon: 'success', title: 'تم إرسال بلاغك!', text: 'شكراً لك، سيقوم فريقنا بمراجعة البلاغ.', confirmButtonText: 'حسناً' });
                        } else {
                            Swal.fire({ icon: 'error', title: 'حدث خطأ', text: data.message || 'لم نتمكن من إرسال بلاغك.', confirmButtonText: 'موافق' });
                        }
                    })
                    .catch(error => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('reportModal'));
                        modal.hide();
                        Swal.fire({ icon: 'error', title: 'خطأ في الاتصال', text: 'فشل الاتصال بالخادم.', confirmButtonText: 'موافق' });
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = submitBtnOriginalText;
                        reportForm.reset();
                    });
                });
            }
        });
    </script>
</body>
</html>




