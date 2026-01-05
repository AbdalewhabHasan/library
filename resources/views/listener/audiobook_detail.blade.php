<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل: {{ $audioBook->title }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token(  ) }}">

    {{-- ▼▼▼ مكتبات ضرورية للنوافذ المنبثقة (مهم جداً) ▼▼▼ --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

    <style>
        :root {
            --primary-color: #6a11cb; --secondary-color: #2575fc; --dark-bg: #121212; --card-bg: #1e1e1e;
            --text-primary: #ffffff; --text-secondary: #b3b3b3; --accent-color: #1DB954; --danger-color: #e74c3c;
            --shadow-light: rgba(255, 255, 255, 0.1 ); --shadow-dark: rgba(0, 0, 0, 0.5); --radius-md: 12px; --radius-lg: 20px; --transition: 0.3s ease;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Tajawal', sans-serif; background-color: var(--dark-bg); color: var(--text-primary); line-height: 1.7; padding: 2rem; }
        .main-container { max-width: 1200px; margin: 2rem auto; position: relative; }
        .back-button-container { position: absolute; top: -1rem; right: 0; z-index: 100; }
        .btn-back { display: inline-flex; align-items: center; gap: 10px; background: rgba(255, 255, 255, 0.1); color: var(--text-primary); padding: 0.75rem 1.5rem; border-radius: 50px; text-decoration: none; font-weight: 700; border: 1px solid transparent; backdrop-filter: blur(10px); transition: var(--transition); }
        .btn-back:hover { background: rgba(255, 255, 255, 0.2); border-color: var(--shadow-light); transform: translateY(-2px); }
        .book-header { display: flex; flex-direction: column; align-items: center; gap: 2rem; padding: 3rem 2rem; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: var(--radius-lg); text-align: center; box-shadow: 0 10px 30px var(--shadow-dark); margin-bottom: 3rem; }
        @media (min-width: 768px) { .book-header { flex-direction: row; text-align: right; } }
        .cover-image { width: 200px; height: 200px; border-radius: 50%; object-fit: cover; border: 5px solid var(--dark-bg); box-shadow: 0 5px 25px var(--shadow-dark); flex-shrink: 0; }
        .header-details h1 { font-size: 2.8rem; font-weight: 800; margin-bottom: 0.5rem; text-shadow: 0 2px 5px var(--shadow-dark); }
        .header-details .author { font-size: 1.5rem; font-weight: 500; color: rgba(255, 255, 255, 0.8); margin-bottom: 1rem; }
        .header-meta { display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center; }
        @media (min-width: 768px) { .header-meta { justify-content: flex-start; } }
        .meta-item { background: rgba(0, 0, 0, 0.2); padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.9rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; }
        .main-content { display: grid; grid-template-columns: 1fr; gap: 2rem; }
        @media (min-width: 992px) { .main-content { grid-template-columns: 3fr 1fr; } }
        .content-card { background: var(--card-bg); padding: 2rem; border-radius: var(--radius-md); box-shadow: 0 4px 20px var(--shadow-dark); }
        .section-title { font-size: 1.8rem; font-weight: 700; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 2px solid var(--primary-color); display: inline-block; }
        .player-section { margin-bottom: 2rem; }
        audio { width: 100%; filter: invert(1) sepia(1) saturate(0.5) hue-rotate(180deg); }
        .description-text { font-size: 1.1rem; color: var(--text-secondary); white-space: pre-wrap; }
        .comments-list { max-height: 400px; overflow-y: auto; padding-left: 1rem; margin-bottom: 2rem; }
        .comment-item { display: flex; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--shadow-light); animation: fadeIn 0.5s ease; position: relative; }
        .comment-item:last-child { border-bottom: none; }
        .comment-avatar { width: 45px; height: 45px; border-radius: 50%; background-color: var(--primary-color); color: var(--text-primary); display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; }
        .comment-body { flex-grow: 1; }
        .comment-body .user-name { font-weight: 700; margin-bottom: 0.25rem; }
        .comment-body .comment-text { color: var(--text-secondary); }
        .sidebar .info-box { margin-bottom: 1.5rem; }
        .sidebar .info-box h4 { font-size: 1rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 0.5rem; }
        .sidebar .info-box p, .sidebar .info-box a { font-size: 1.1rem; font-weight: 500; text-decoration: none; color: var(--text-primary); }
        .sidebar .info-box a:hover { color: var(--secondary-color); }
        .rating-section { background: rgba(0,0,0,0.2); padding: 1rem; border-radius: var(--radius-md); text-align: center; }
        .rating-stars .fa-star { font-size: 1.8rem; color: var(--text-secondary); cursor: pointer; transition: var(--transition); }
        .rating-stars .fa-star:hover { transform: scale(1.2); }
        .rating-stars .fa-star.active { color: #ffc107; }
        .form-control { width: 100%; background: rgba(0,0,0,0.2); border: 1px solid var(--shadow-light); border-radius: var(--radius-md); padding: 1rem; color: var(--text-primary); font-family: 'Tajawal', sans-serif; font-size: 1rem; }
        .form-control:focus { outline: none; border-color: var(--secondary-color); }
        .btn-submit { width: 100%; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border: none; color: var(--text-primary); padding: 1rem; font-size: 1.1rem; font-weight: 700; border-radius: var(--radius-md); cursor: pointer; transition: var(--transition); }
        .btn-submit:hover { transform: scale(1.02); box-shadow: 0 5px 15px var(--shadow-dark); }
        .btn-submit:disabled { background: var(--text-secondary); cursor: not-allowed; }
        .notification { position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background-color: var(--accent-color); color: white; padding: 1rem 2rem; border-radius: 50px; box-shadow: 0 5px 20px var(--shadow-dark); z-index: 1001; opacity: 0; transition: opacity 0.5s, top 0.5s; }
        .notification.show { opacity: 1; top: 40px; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .btn-report-book { background: linear-gradient(135deg, #c0392b, #e74c3c); }
        .btn-report-comment { position: absolute; top: 1.5rem; left: 0; background: none; border: none; color: var(--text-secondary); font-size: 0.9rem; cursor: pointer; opacity: 0.5; transition: var(--transition); }
        .btn-report-comment:hover { opacity: 1; color: var(--danger-color); transform: scale(1.1); }
    </style>
</head>
<body>

    <div class="main-container">
        <div class="back-button-container">
            <a href="{{ $back_route ?? url()->previous() }}" class="btn-back"><i class="fas fa-arrow-right"></i> <span>عودة</span></a>
        </div>

        <header class="book-header">
            <img src="{{ $audioBook->cover_image_path ? asset('storage/' . $audioBook->cover_image_path) : 'https://via.placeholder.com/200.png?text=Cover' }}" alt="غلاف الكتاب" class="cover-image">
            <div class="header-details">
                <h1>{{ $audioBook->title }}</h1>
                <p class="author">بواسطة: {{ $audioBook->author }}</p>
                <div class="header-meta">
                    <span class="meta-item"><i class="fas fa-microphone-alt"></i> {{ $audioBook->narrator }}</span>
                    <span class="meta-item"><i class="fas fa-tag"></i> {{ $audioBook->category->name ?? 'غير مصنف' }}</span>
                    <span class="meta-item"><i class="fas fa-language"></i> {{ $audioBook->language }}</span>
                </div>
            </div>
        </header>

        <main class="main-content">
            <div class="content-card">
                <section class="player-section">
                    <h2 class="section-title">استمع الآن</h2>
                    @if($audioBook->file_path && Storage::disk('public'  )->exists($audioBook->file_path))
                        <audio id="audio-player" controls><source src="{{ Storage::url($audioBook->file_path) }}" type="audio/mpeg">متصفحك لا يدعم تشغيل الصوت.</audio>
                    @else
                        <p>عذراً، الملف الصوتي غير متاح حالياً.</p>
                    @endif
                </section>
                <section class="description-section">
                    <h2 class="section-title">عن الكتاب</h2>
                    <p class="description-text">{{ $audioBook->description ?? 'لا يوجد وصف متاح.' }}</p>
                </section>
            </div>

            <aside class="sidebar">
                <div class="content-card">
                    <div class="info-box">
                        <h4>الناشر</h4>
                        <p>@if($audioBook->publisher)<a href="{{ route('listener.publisher.audioBooks', $audioBook->publisher->id) }}">{{ $audioBook->publisher->name }}</a>@else غير محدد @endif</p>
                    </div>
                    <div class="info-box">
                        <h4>متوسط التقييم</h4>
                        <p id="average-rating-display"><i class="fas fa-star" style="color: #ffc107;"></i> {{ number_format($audioBook->ratings->avg('rating') ?? 0, 1) }} / 5.0</p>
                    </div>

                    <div class="info-box">
                        <h4>مشاركة</h4>
                        <button id="shareButton" class="btn-submit" style="width: auto; padding: 0.5rem 1.5rem; background: linear-gradient(135deg, #36D1DC, #5B86E5); display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-share-alt"></i> مشاركة الكتاب
                        </button>
                    </div>

                    <div class="info-box">
                        <h4>إجراءات</h4>
                        {{-- ▼▼▼ التعديل الأول: زر الإبلاغ عن الكتاب ▼▼▼ --}}
                        <button type="button" class="btn-submit btn-report-book"
                                onclick="openGlobalReportModal('App\\Models\\AudioBook', {{ $audioBook->id }}, '{{ e($audioBook->title) }}')">
                            <i class="fas fa-flag"></i> إبلاغ عن الكتاب
                        </button>
                    </div>

                    <div class="info-box">
                        <h4>تقييمك الشخصي</h4>
                        <div class="rating-section">
                            <div class="rating-stars" data-audiobook-id="{{ $audioBook->id }}">
                             @php $userRatingValue = isset($userRating) && $userRating ? $userRating->rating : 0; @endphp

                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $i <= $userRatingValue ? 'active' : '' }}" data-rating="{{ $i }}"></i>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </main>

        <section class="content-card" style="margin-top: 2rem;">
            <h2 class="section-title" id="comments-count-display">التعليقات ({{ $audioBook->comments->count() }})</h2>
            <form id="comment-form" action="{{ route('listener.comments.add', $audioBook->id) }}" method="POST" style="margin-bottom: 2rem;">
                @csrf
                <textarea name="comment" class="form-control" rows="3" placeholder="اكتب تعليقك هنا..." required></textarea>
                <button type="submit" class="btn-submit" style="margin-top: 1rem;">إضافة تعليق</button>
            </form>
            <div class="comments-list" id="comments-list-container">
                @forelse ($audioBook->comments->sortByDesc('created_at') as $comment)
                    <div class="comment-item">
                        <div class="comment-avatar">{{ mb_substr($comment->user->name, 0, 1) }}</div>
                        <div class="comment-body">
                            <p class="user-name">{{ $comment->user->name }}</p>
                            <p class="comment-text">{{ $comment->comment }}</p>
                        </div>
                        {{-- ▼▼▼ التعديل الثاني: زر الإبلاغ عن التعليق ▼▼▼ --}}
                        <button type="button" class="btn-report-comment"
                                title="الإبلاغ عن هذا التعليق"
                                onclick="openGlobalReportModal('App\\Models\\Comment', {{ $comment->id }}, 'تعليق المستخدم: {{ e($comment->user->name) }}')">
                            <i class="fas fa-flag"></i>
                        </button>
                    </div>
                @empty
                    <p id="no-comments-message">لا توجد تعليقات بعد. كن أول من يعلق!</p>
                @endforelse
            </div>
        </section>
    </div>

    <div id="notification-toast" class="notification"></div>

    <button id="voice-command-btn" style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(145deg, #5c67e3, #8f6ed5); color: white; border: none; font-size: 24px; cursor: pointer; z-index: 9999; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2); transition: all 0.3s ease;">🎤</button>
    <div id="voice-feedback" style="position: fixed; bottom: 100px; right: 30px; background-color: rgba(0,0,0,0.8); color: white; padding: 10px 15px; border-radius: 8px; display: none; z-index: 10000; box-shadow: 0 4px 15px rgba(0,0,0,0.2);"></div>

    <div id="shareModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); backdrop-filter: blur(5px); z-index: 10000; align-items: center; justify-content: center;">
        <div id="shareModalContent" style="background: #2d3748; color: white; padding: 2rem; border-radius: 15px; width: 90%; max-width: 450px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.4); position: relative; border: 1px solid var(--shadow-light);">
            <button id="closeModal" style="position: absolute; top: 10px; right: 15px; background: none; border: none; color: white; font-size: 2rem; cursor: pointer; line-height: 1;">&times;</button>
            <h4 style="margin-bottom: 1.5rem; font-family: 'Poppins', sans-serif; font-size: 1.5rem;">مشاركة "{{ $audioBook->title }}"</h4>
            <p style="text-align: right; margin-bottom: 0.5rem; color: var(--text-secondary);">انسخ الرابط:</p>
            <div style="display: flex; margin-bottom: 2rem;">
                <input type="text" id="shareLinkInput" value="{{ url()->current() }}" readonly style="width: 100%; padding: 0.75rem; border: 1px solid #4a5568; background: #1a202c; color: white; border-radius: 8px 0 0 8px; outline: none; font-size: 1rem;">
                <button id="copyLinkButton" style="padding: 0.75rem 1rem; border: none; background: var(--secondary-color); color: white; cursor: pointer; border-radius: 0 8px 8px 0; font-weight: 700;">نسخ</button>
            </div>
            <p style="margin-bottom: 1rem; color: var(--text-secondary);">أو شارك مباشرة عبر:</p>
            <div id="socialShareButtons" style="display: flex; justify-content: center; gap: 1.5rem;">
                <a href="https://api.whatsapp.com/send?text={{ urlencode('استمع إلى كتاب: ' . $audioBook->title . ' على منصتنا! ' . url(  )->current()) }}" target="_blank" style="font-size: 2.5rem; color: #25D366; transition: transform 0.2s ease;"><i class="fab fa-whatsapp-square"></i></a>
                <a href="https://twitter.com/intent/tweet?text={{ urlencode('أستمع الآن إلى كتاب: ' . $audioBook->title   ) }}&url={{ url()->current() }}" target="_blank" style="font-size: 2.5rem; color: #1DA1F2; transition: transform 0.2s ease;"><i class="fab fa-twitter-square"></i></a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ url(  )->current() }}" target="_blank" style="font-size: 2.5rem; color: #1877F2; transition: transform 0.2s ease;"><i class="fab fa-facebook-square"></i></a>
            </div>
        </div>
    </div>

    {{-- ▼▼▼ التعديل الثالث: إضافة النافذة المنبثقة (Modal) ▼▼▼ --}}
    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: var(--card-bg); color: var(--text-primary);">
                <form id="reportFormModal">
                    <div class="modal-header" style="border-bottom-color: var(--shadow-light);">
                        <h5 class="modal-title" id="reportModalLabel">إبلاغ عن محتوى</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="reportableType" name="reportable_type">
                        <input type="hidden" id="reportableId" name="reportable_id">
                        <div class="mb-3">
                            <label for="reportReason" class="form-label">سبب الإبلاغ:</label>
                            <textarea class="form-control" id="reportReason" name="reason" rows="4" required minlength="10" placeholder="الرجاء تقديم وصف واضح للمشكلة..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top-color: var(--shadow-light);">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger" id="submitReportBtn">إرسال الإبلاغ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
    // ▼▼▼ الدالة العامة لفتح نافذة الإبلاغ ▼▼▼
    function openGlobalReportModal(type, id, name) {
        const reportModalElement = document.getElementById('reportModal');
        if (!reportModalElement || typeof bootstrap === 'undefined') {
            console.error('Modal or Bootstrap is not available.');
            alert('حدث خطأ، لا يمكن فتح نافذة الإبلاغ.');
            return;
        }
        const reportModal = new bootstrap.Modal(reportModalElement);
        document.getElementById('reportModalLabel').textContent = `إبلاغ عن: "${name}"`;
        document.getElementById('reportableType').value = type;
        document.getElementById('reportableId').value = id;
        reportModal.show();
    }

    document.addEventListener('DOMContentLoaded', () => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // ▼▼▼ معالج فورم الإبلاغ ▼▼▼
        const reportFormModal = document.getElementById('reportFormModal');
        if (reportFormModal) {
            reportFormModal.addEventListener('submit', function (e) {
                e.preventDefault();
                const submitBtn = document.getElementById('submitReportBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> جاري الإرسال...`;

                fetch("{{ route('listener.reports.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
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
                    const modal = bootstrap.Modal.getInstance(reportFormModal.closest('.modal'));
                    modal.hide();
                    if (data.success) {
                        Swal.fire({
                            title: 'تم الإرسال!',
                            text: 'شكراً لك، سيتم مراجعة بلاغك.',
                            icon: 'success',
                            confirmButtonText: 'حسناً',
                            background: 'var(--card-bg)',
                            color: 'var(--text-primary)'
                        });
                    } else {
                        Swal.fire({
                            title: 'خطأ!',
                            text: data.message || 'لم نتمكن من إرسال بلاغك.',
                            icon: 'error',
                            confirmButtonText: 'موافق',
                            background: 'var(--card-bg)',
                            color: 'var(--text-primary)'
                        });
                    }
                })
                .catch(error => {
                    const modal = bootstrap.Modal.getInstance(reportFormModal.closest('.modal'));
                    modal.hide();
                    Swal.fire({
                        title: 'خطأ في الاتصال!',
                        text: 'فشل الاتصال بالخادم.',
                        icon: 'error',
                        confirmButtonText: 'موافق',
                        background: 'var(--card-bg)',
                        color: 'var(--text-primary)'
                    });
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'إرسال الإبلاغ';
                    reportFormModal.reset();
                });
            });
        }

        // --- بداية الكود الأصلي (لا تغيير هنا) ---
        const audioBookId = "{{ $audioBook->id }}";

        function showNotification(message, type = 'success') {
            const notificationToast = document.getElementById('notification-toast');
            if (!notificationToast) return;
            notificationToast.textContent = message;
            notificationToast.style.backgroundColor = type === 'error' ? '#e74c3c' : 'var(--accent-color)';
            notificationToast.classList.add('show');
            setTimeout(() => { notificationToast.classList.remove('show'); }, 3000);
        }

        const ratingStarsContainer = document.querySelector('.rating-stars');
        if (ratingStarsContainer) {
            const stars = ratingStarsContainer.querySelectorAll('.fa-star');
            stars.forEach(star => star.addEventListener('click', () => submitRating(star.dataset.rating)));

            function submitRating(rating) {
                showNotification('جاري تسجيل تقييمك...');
                fetch("{{ route('listener.rate', $audioBook->id) }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ rating: rating })
                }).then(res => res.json()).then(data => {
                    if (data.success) {
                        showNotification('تم تسجيل تقييمك بنجاح!');
                        updateStarsUI(rating);
                        fetchAverageRating();
                    } else { showNotification('حدث خطأ أثناء التقييم.', 'error'); }
                }).catch(() => showNotification('فشل الاتصال بالخادم.', 'error'));
            }

            function updateStarsUI(rating) {
                stars.forEach(star => star.classList.toggle('active', star.dataset.rating <= rating));
            }

            function fetchAverageRating() {
                fetch("{{ route('api.audiobook.rating', $audioBook->id) }}")
                .then(res => res.json()).then(data => {
                    const avgDisplay = document.getElementById('average-rating-display');
                    if (avgDisplay && data.average_rating !== undefined) {
                        avgDisplay.innerHTML = `<i class="fas fa-star" style="color: #ffc107;"></i> ${Number(data.average_rating).toFixed(1)} / 5.0`;
                    }
                });
            }
        }

        const commentForm = document.getElementById('comment-form');
        if (commentForm) {
            commentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const textarea = this.querySelector('textarea');
                const button = this.querySelector('button');
                if (!textarea.value.trim()) return;

                button.disabled = true;
                button.textContent = 'جاري الإرسال...';

                fetch(this.action, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ comment: textarea.value })
                }).then(res => res.json()).then(data => {
                    if (data.success && data.comment) {
                        showNotification('تمت إضافة تعليقك بنجاح!');
                        textarea.value = '';
                        addCommentToUI(data.comment);
                    } else { showNotification(data.message || 'حدث خطأ.', 'error'); }
                }).catch(() => showNotification('فشل الاتصال بالخادم.', 'error'))
                .finally(() => {
                    button.disabled = false;
                    button.textContent = 'إضافة تعليق';
                });
            });
        }
        function addCommentToUI(comment) {
            const container = document.getElementById('comments-list-container');
            const noMsg = document.getElementById('no-comments-message');
            const countDisplay = document.getElementById('comments-count-display');
            if (noMsg) noMsg.remove();

            const newComment = document.createElement('div');
            newComment.className = 'comment-item';

            // Function to escape single quotes for the onclick attribute
            const escapeSingleQuotes = (str) => str.replace(/'/g, "\\'");

            newComment.innerHTML = `
                <div class="comment-avatar">${comment.user.name.substring(0, 1)}</div>
                <div class="comment-body">
                    <p class="user-name">${comment.user.name}</p>
                    <p class="comment-text">${comment.comment}</p>
                </div>
                <button type="button" class="btn-report-comment"
                        title="الإبلاغ عن هذا التعليق"
                        onclick="openGlobalReportModal('App\\Models\\Comment', ${comment.id}, 'تعليق المستخدم: ${escapeSingleQuotes(comment.user.name)}');">
                    <i class="fas fa-flag"></i>
                </button>`;
            container.prepend(newComment);

            if(countDisplay) {
                const currentCount = parseInt(countDisplay.textContent.match(/\d+/)[0] || 0);
                countDisplay.textContent = `التعليقات (${currentCount + 1})`;
            }
        }

        const voiceBtn = document.getElementById('voice-command-btn');
        const voiceFeedback = document.getElementById('voice-feedback');
        const audioPlayer = document.getElementById('audio-player');

        if (('SpeechRecognition' in window || 'webkitSpeechRecognition' in window) && voiceBtn) {
            const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
            recognition.lang = 'ar-SA';
            recognition.interimResults = false;

            voiceBtn.addEventListener('click', () => {
                showVoiceFeedback('استمع الآن...');
                try { recognition.start(); } catch(e) { console.error(e); }
            });

            recognition.onresult = (event) => {
                const command = event.results[0][0].transcript.trim();
                showVoiceFeedback(`سمعتك تقول: "${command}"`);
                handleVoiceCommand(command);
            };
            recognition.onerror = (event) => showVoiceFeedback(`خطأ: ${event.error}`);

        } else if (voiceBtn) {
            voiceBtn.style.display = 'none';
        }

        function handleVoiceCommand(command) {
            const commandLower = command.toLowerCase();
            if (commandLower.includes('تشغيل')) { if(audioPlayer) audioPlayer.play(); return; }
            if (commandLower.includes('إيقاف')) { if(audioPlayer) audioPlayer.pause(); return; }
            const rateMatch = command.match(/(?:تقييم|قيم)\s*(\d+|نجمة|نجمتين|ثلاث|اربع|خمس)/);
            if (rateMatch) {
                const ratingsMap = { 'نجمة': 1, 'نجمتين': 2, 'ثلاث': 3, 'اربع': 4, 'خمس': 5 };
                const ratingValue = ratingsMap[rateMatch[1]] || parseInt(rateMatch[1]);
                if(ratingValue && ratingStarsContainer) submitRating(ratingValue);
                return;
            }
            if (commandLower.startsWith("اكتب تعليق")) {
                const commentText = command.substring("اكتب تعليق".length).trim();
                const textarea = commentForm.querySelector('textarea');
                const submitBtn = commentForm.querySelector('button');
                if (textarea && submitBtn && commentText) {
                    textarea.value = commentText;
                    submitBtn.click();
                }
                return;
            }
            showVoiceFeedback('أمر غير معروف.');
        }

        function showVoiceFeedback(message) {
            if(voiceFeedback) {
                voiceFeedback.textContent = message;
                voiceFeedback.style.display = 'block';
                setTimeout(() => { voiceFeedback.style.display = 'none'; }, 4000);
            }
        }

        const shareButton = document.getElementById('shareButton');
        const shareModal = document.getElementById('shareModal');
        const closeModal = document.getElementById('closeModal');
        const copyLinkButton = document.getElementById('copyLinkButton');
        const shareLinkInput = document.getElementById('shareLinkInput');

        if (shareButton) {
            shareButton.addEventListener('click', () => {
                if (shareModal) shareModal.style.display = 'flex';
            });
        }
        if (closeModal) {
            closeModal.addEventListener('click', () => {
                if (shareModal) shareModal.style.display = 'none';
            });
        }
        if (shareModal) {
            shareModal.addEventListener('click', (event) => {
                if (event.target === shareModal) {
                    shareModal.style.display = 'none';
                }
            });
        }
        if (copyLinkButton) {
            copyLinkButton.addEventListener('click', () => {
                shareLinkInput.select();
                shareLinkInput.setSelectionRange(0, 99999);
                navigator.clipboard.writeText(shareLinkInput.value).then(() => {
                    copyLinkButton.textContent = 'تم النسخ!';
                    setTimeout(() => { copyLinkButton.textContent = 'نسخ'; }, 2000);
                }).catch(err => console.error('فشل النسخ: ', err));
            });
        }
    });
</script>

</body>
</html>
