{{-- ====================================================================== --}}
{{-- ==   صفحة كل الكتب (مع تصميم "لا توجد نتائج" الاحترافي)             == --}}
{{-- ==   File: resources/views/publisher/audio-books/all.blade.php     == --}}
{{-- ====================================================================== --}}

@extends('layouts.publisher')

@section('title', 'اكتشف عالم الكتب الصوتية')

@push('styles')
<style>
    /* نفس الـ CSS الأسطوري السابق */
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --shadow-light: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        --bg-color: #f0f2f5;
        --card-bg: rgba(255, 255, 255, 0.9);
        --card-border: rgba(0, 0, 0, 0.05);
        --text-color: #1a202c;
        --text-muted: #718096;
    }
    html[data-theme='dark'] {
        --bg-color: #1a202c;
        --card-bg: rgba(26, 32, 44, 0.7);
        --card-border: rgba(255, 255, 255, 0.1);
        --text-color: #edf2f7;
        --text-muted: #a0aec0;
    }
    body {
        background-color: var(--bg-color);
        color: var(--text-color);
        font-family: 'Cairo', sans-serif;
    }
    .main-container { padding: 2rem 0; }
    .header-card {
        background: var(--primary-gradient);
        color: white;
        border-radius: 25px;
        padding: 3rem;
        text-align: center;
    }
    .filters-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--card-border);
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: var(--shadow-light);
    }
    .form-control, .form-select {
        background-color: var(--bg-color);
        color: var(--text-color);
        border-color: var(--card-border);
    }
    .book-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--card-border);
        border-radius: 20px;
        box-shadow: var(--shadow-light);
        overflow: hidden;
        transition: all 0.4s ease;
        height: 100%;
    }
    .book-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(31, 38, 135, 0.4);
    }
    .book-card .card-img-top { height: 250px; object-fit: cover; }
    .book-card .card-title { font-weight: 700; color: var(--text-color); }
    .book-card .btn-listen {
        background: var(--primary-gradient);
        border: none;
        font-weight: 600;
        width: 100%;
        padding: 0.75rem;
        border-radius: 10px;
    }
    .pagination .page-item.active .page-link {
        background: var(--primary-gradient);
        border: none;
    }
    .modal-content {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--card-border);
        border-radius: 20px;
    }
    .modal-header { border-bottom: 1px solid var(--card-border); }
    .modal-title { color: var(--text-color); font-weight: 700; }
    .modal-body audio {
        filter: invert(var(--is-dark, 0));
    }
    html[data-theme='dark'] .modal-body audio {
        --is-dark: 1;
    }
    /* ▼▼▼ CSS خاص بتصميم "لا توجد نتائج" ▼▼▼ */
    .no-results-card {
        background: var(--card-bg);
        backdrop-filter: blur(10px);
        border: 1px solid var(--card-border);
        border-radius: 20px;
        padding: 3rem;
        box-shadow: var(--shadow-light);
        text-align: center;
    }
    .no-results-card i {
        font-size: 4rem;
        color: var(--text-muted);
        margin-bottom: 1.5rem;
    }
    .no-results-card h4 {
        color: var(--text-color);
        font-weight: 700;
    }
    .no-results-card p {
        color: var(--text-muted);
    }
</style>
@endpush

@section('content')
<main class="main-container">
    <div class="container">
        <!-- الهيدر والفلاتر (تبقى كما هي) -->
        <div class="header-card mb-5 shadow-lg">
            <h1 class="display-4 fw-bold"><i class="fas fa-book-open me-3"></i>اكتشف عالم الكتب الصوتية</h1>
            <p class="lead">استمع إلى آلاف الكتب في مختلف المجالات، في أي وقت ومن أي مكان.</p>
        </div>
        <div class="filters-card mb-5">
            <form action="{{ route('audio-books.all') }}" method="GET" class="d-flex gap-3">
                <input type="text" name="search" class="form-control form-control-lg" placeholder="ابحث..." value="{{ request('search') }}">
                <select name="category" class="form-select form-select-lg" style="max-width: 250px;">
                    <option value="">كل الفئات</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary btn-lg" type="submit" title="بحث"><i class="fas fa-search"></i></button>
                <a href="{{ route('audio-books.all') }}" class="btn btn-outline-secondary btn-lg" title="إعادة تعيين"><i class="fas fa-sync-alt"></i></a>
            </form>
        </div>

        {{-- ▼▼▼ هذا هو القسم الذي تم تعديله بالكامل ▼▼▼ --}}
        @if($audioBooks->isEmpty())
            <div class="no-results-card">
                <i class="fas fa-search-minus"></i>
                <h4>لا توجد كتب صوتية تطابق بحثك</h4>
                <p>حاول استخدام كلمات بحث مختلفة أو قم بإعادة تعيين الفلاتر لعرض كل الكتب.</p>
            </div>
        @else
            <div class="row g-4">
                @foreach($audioBooks as $book)
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="book-card">
                            @php
                                $hasImage = $book->cover_image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($book->cover_image_path);
                            @endphp
                            <img src="{{ $hasImage ? asset('storage/' . $book->cover_image_path) : 'https://via.placeholder.com/300x200?text=No+Cover' }}" 
                                 class="card-img-top" 
                                 alt="{{ $book->title }}"
                                 onerror="this.src='https://via.placeholder.com/300x200?text=No+Cover'; this.onerror=null;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-truncate" title="{{ $book->title }}">{{ $book->title }}</h5>
                                <p class="card-text small text-muted flex-grow-1">بواسطة: {{ $book->author }}</p>
                                <div class="d-flex justify-content-between small text-muted mb-3">
                                    <span><i class="fas fa-tag me-1"></i> {{ $book->category->name ?? 'غير مصنف' }}</span>
                                    <span><i class="fas fa-clock me-1"></i> {{ $book->duration }} دقيقة</span>
                                </div>
                                <button class="btn btn-primary btn-listen mt-auto" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#audioPlayerModal"
                                        data-audio-src="{{ Storage::url($book->file_path) }}"
                                        data-audio-title="{{ $book->title }}">
                                    استمع الآن
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-center mt-5">
                {{ $audioBooks->appends(request()->query())->links() }}
            </div>
        @endif
        {{-- ▲▲▲ انتهى القسم المعدل ▲▲▲ --}}
    </div>
</main>
@endsection

{{-- المودال والـ JavaScript (تبقى كما هي) --}}
<div class="modal fade" id="audioPlayerModal" tabindex="-1" aria-labelledby="audioPlayerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="audioPlayerModalLabel">جاري الاستماع إلى...</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <i class="fas fa-headphones-alt fa-4x text-primary mb-3"></i>
                <h4 id="modalAudioTitle" class="mb-4">عنوان الكتاب</h4>
                <audio id="modalAudioPlayer" controls class="w-100"></audio>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const audioPlayerModal = document.getElementById('audioPlayerModal');
    if (audioPlayerModal) {
        const modalAudioPlayer = document.getElementById('modalAudioPlayer');
        const modalAudioTitle = document.getElementById('modalAudioTitle');

        audioPlayerModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const audioSrc = button.getAttribute('data-audio-src');
            const audioTitle = button.getAttribute('data-audio-title');
            modalAudioTitle.textContent = audioTitle;
            modalAudioPlayer.src = audioSrc;
            modalAudioPlayer.play();
        });

        audioPlayerModal.addEventListener('hide.bs.modal', function () {
            modalAudioPlayer.pause();
            modalAudioPlayer.currentTime = 0;
            modalAudioPlayer.src = '';
        });
    }
});
</script>
@endpush
