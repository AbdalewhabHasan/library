{{-- ====================================================================== --}}
{{-- ==   صفحة عرض كتب الناشر الخاصة (مع فلاتر وعرض شبكي/جدولي)         == --}}
{{-- ==   File: resources/views/publisher/audio-books/index.blade.php    == --}}
{{-- ====================================================================== --}}

{{-- 1. أخبر هذه الصفحة أنها ترث من ليآوت الناشر الموحد --}}
@extends('layouts.publisher')

{{-- 2. حدد عنوان الصفحة --}}
@section('title', 'كتبي الصوتية')

{{-- 3. أضف الـ CSS الخاص بهذه الصفحة فقط --}}
@push('styles')
<style>
    /* هذا الـ CSS خاص فقط بصفحة عرض الكتب */
    .header-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    html[data-bs-theme='dark'] .glass-card {
        background: rgba(26, 32, 44, 0.8);
        border-color: rgba(255, 255, 255, 0.1);
    }
    .controls-card {
        background: linear-gradient(135deg, #3a475a, #2c3e50);
        color: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }
    .premium-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 15px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    html[data-bs-theme='dark'] .premium-card {
        background: rgba(26, 32, 44, 0.8);
        border-color: rgba(255, 255, 255, 0.1);
    }
    .premium-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }
    .card-img-container {
        height: 280px;
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #f0f0f0, #e0e0e0);
    }
    html[data-bs-theme='dark'] .card-img-container {
        background: linear-gradient(135deg, #2d3748, #1a202c);
    }
    .card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 50%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .premium-card:hover .card-overlay {
        opacity: 1;
    }
    .card-img-top {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .premium-card:hover .card-img-top {
        transform: scale(1.1);
    }
    .table-img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
    .empty-state-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        padding: 3rem;
        text-align: center;
    }
    html[data-bs-theme='dark'] .empty-state-card {
        background: rgba(26, 32, 44, 0.8);
    }
    .description-text {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        min-height: 40px;
        line-height: 1.5;
    }
    .form-control, .form-select {
        background-color: rgba(255, 255, 255, 0.9);
        color: #1a202c;
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }
    html[data-bs-theme='dark'] .form-control,
    html[data-bs-theme='dark'] .form-select {
        background-color: rgba(26, 32, 44, 0.8);
        color: #edf2f7;
        border-color: rgba(255, 255, 255, 0.1);
    }
    .form-control:focus, .form-select:focus {
        background-color: rgba(255, 255, 255, 0.95);
        color: #1a202c;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        outline: none;
    }
    html[data-bs-theme='dark'] .form-control:focus,
    html[data-bs-theme='dark'] .form-select:focus {
        background-color: rgba(26, 32, 44, 0.9);
        color: #edf2f7;
    }
    .modal-content {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 15px;
    }
    html[data-bs-theme='dark'] .modal-content {
        background: rgba(26, 32, 44, 0.95);
        border-color: rgba(255, 255, 255, 0.1);
    }
    /* تحسينات إضافية */
    .btn-outline-warning {
        border: 2px solid #ffc107;
        color: #ffc107;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-outline-warning:hover {
        background: #ffc107;
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
    }
    .btn-outline-danger {
        border: 2px solid #dc3545;
        color: #dc3545;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-outline-danger:hover {
        background: #dc3545;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
    }
    .audio-book-item {
        animation: fadeInUp 0.5s ease forwards;
        opacity: 0;
    }
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .audio-book-item:nth-child(1) { animation-delay: 0.1s; }
    .audio-book-item:nth-child(2) { animation-delay: 0.2s; }
    .audio-book-item:nth-child(3) { animation-delay: 0.3s; }
    .audio-book-item:nth-child(4) { animation-delay: 0.4s; }
</style>
@endpush

{{-- 4. هذا هو المحتوى الخاص بصفحة عرض الكتب فقط --}}
@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="header-card p-4 rounded-4 shadow-lg">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h2 class="h3 mb-1 text-white fw-bold"><i class="fas fa-headphones me-3"></i>كتبي الصوتية</h2>
                        <p class="text-white-50 mb-0">إدارة وعرض مجموعة الكتب الصوتية الخاصة بك</p>
                    </div>
                    <div class="d-flex align-items-center mt-3 mt-md-0">
                        <a href="{{ route('publisher.dashboard') }}" class="btn btn-outline-light me-3"><i class="fas fa-arrow-left me-2"></i>العودة للوحة التحكم</a>
                        <a href="{{ route('publisher.audio-books.create') }}" class="btn btn-warning"><i class="fas fa-plus me-2"></i>إضافة كتاب جديد</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($audioBooks->isEmpty())
        <div class="row">
            <div class="col-12">
                <div class="empty-state-card text-center py-5 rounded-4 shadow-lg">
                    <i class="fas fa-music display-1 text-primary mb-4"></i>
                    <h3 class="h4 mb-3">لا توجد كتب صوتية بعد</h3>
                    <p class="text-muted mb-4">لم تقم بإضافة أي كتب صوتية. ابدأ ببناء مكتبتك الآن!</p>
                    <a href="{{ route('publisher.audio-books.create') }}" class="btn btn-primary btn-lg"><i class="fas fa-plus me-2"></i>إضافة أول كتاب</a>
                </div>
            </div>
        </div>
    @else
        <!-- Search and Filter Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="glass-card p-3 rounded-4 shadow-lg">
                    <div class="row g-2 align-items-center">
                        <div class="col-lg-5">
                            <input type="text" id="searchInput" class="form-control form-control-lg" placeholder="ابحث بالاسم، المؤلف...">
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <select id="categoryFilter" class="form-select form-select-lg">
                                <option value="">كل الفئات</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->name }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <select id="statusFilter" class="form-select form-select-lg">
                                <option value="">كل الحالات</option>
                                <option value="approved">مقبول</option>
                                <option value="pending">قيد المراجعة</option>
                                <option value="rejected">مرفوض</option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-4">
                            <button id="clearFilters" class="btn btn-outline-danger btn-lg w-100">
                                <i class="fas fa-times me-2"></i>مسح الفلاتر
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Toggle and Stats -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="controls-card p-3 rounded-4 shadow-lg">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="btn-group shadow-sm" role="group">
                            <button type="button" id="gridViewBtn" class="btn btn-light active"><i class="fas fa-th me-2"></i>شبكي</button>
                            <button type="button" id="tableViewBtn" class="btn btn-outline-light"><i class="fas fa-list me-2"></i>جدول</button>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-dark me-2 fs-6 px-3 py-2">إجمالي: <span id="totalCount">{{ $audioBooks->count() }}</span></span>
                            <span class="badge bg-warning text-dark fs-6 px-3 py-2">المعروض: <span id="visibleCount">{{ $audioBooks->count() }}</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid View -->
        <div id="gridContainer" class="row g-4">
            @foreach($audioBooks as $book)
                <div class="col-xl-3 col-lg-4 col-md-6 audio-book-item" data-title="{{ strtolower($book->title) }}" data-author="{{ strtolower($book->author) }}" data-category="{{ $book->category->name ?? '' }}" data-status="{{ $book->status }}">
                    <div class="card h-100 premium-card shadow-lg border-0">
                        <div class="card-img-container">
                            @php
                                $hasImage = $book->cover_image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($book->cover_image_path);
                            @endphp
                            <img src="{{ $hasImage ? asset('storage/' . $book->cover_image_path) : 'https://via.placeholder.com/300x200?text=No+Cover' }}" 
                                 alt="{{ $book->title }}" 
                                 class="card-img-top h-100 object-fit-cover"
                                 onerror="this.src='https://via.placeholder.com/300x200?text=No+Cover'; this.onerror=null;">
                            <div class="card-overlay"></div>
                            <span class="badge position-absolute top-0 start-0 m-3 fs-6 
                                @if($book->status == 'approved') bg-success @elseif($book->status == 'pending') bg-warning text-dark @else bg-danger @endif">
                                {{ $book->status == 'approved' ? 'مقبول' : ($book->status == 'pending' ? 'قيد المراجعة' : 'مرفوض') }}
                            </span>
                        </div>
                        <div class="card-body d-flex flex-column p-3">
                            <h5 class="card-title text-truncate fw-bold" title="{{ $book->title }}">{{ $book->title }}</h5>
                            <p class="text-muted small mb-2">بواسطة: {{ $book->author }}</p>
                            <p class="card-text text-muted flex-grow-1 description-text small">{{ $book->description }}</p>
                            <div class="d-flex gap-2 mt-auto pt-2 border-top border-light-subtle">
                                <a href="{{ route('publisher.audio-books.edit', $book->id) }}" class="btn btn-sm btn-outline-warning flex-fill"><i class="fas fa-edit"></i> تعديل</a>
                                <button onclick="showDeleteModal({{ $book->id }}, '{{ $book->title }}')" class="btn btn-sm btn-outline-danger flex-fill"><i class="fas fa-trash"></i> حذف</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Table View -->
        <div id="tableContainer" class="d-none">
            <div class="card premium-card shadow-lg border-0 rounded-4">
                <div class="table-responsive">
                    <table class="table table-hover table-borderless align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>الغلاف</th><th>العنوان</th><th>المؤلف</th><th>الفئة</th><th>الحالة</th><th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($audioBooks as $book)
                                <tr class="audio-book-item" data-title="{{ strtolower($book->title) }}" data-author="{{ strtolower($book->author) }}" data-category="{{ $book->category->name ?? '' }}" data-status="{{ $book->status }}">
                                    @php
                                        $hasImage = $book->cover_image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($book->cover_image_path);
                                    @endphp
                                    <td><img src="{{ $hasImage ? asset('storage/' . $book->cover_image_path) : 'https://via.placeholder.com/60x60?text=No+Cover' }}" 
                                             alt="{{ $book->title }}" 
                                             class="rounded-3 shadow-sm table-img"
                                             onerror="this.src='https://via.placeholder.com/60x60?text=No+Cover'; this.onerror=null;"></td>
                                    <td class="fw-bold">{{ $book->title }}</td>
                                    <td>{{ $book->author }}</td>
                                    <td><span class="badge bg-primary">{{ $book->category->name ?? 'N/A' }}</span></td>
                                    <td><span class="badge @if($book->status == 'approved') bg-success @elseif($book->status == 'pending') bg-warning text-dark @else bg-danger @endif">{{ $book->status == 'approved' ? 'مقبول' : ($book->status == 'pending' ? 'قيد المراجعة' : 'مرفوض') }}</span></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('publisher.audio-books.edit', $book->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                            <button onclick="showDeleteModal({{ $book->id }}, '{{ $book->title }}')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- No Results Message -->
        <div id="noResults" class="d-none text-center py-5 empty-state-card rounded-4 shadow-lg">
            <i class="fas fa-search fa-4x text-muted mb-3"></i>
            <h4>لا توجد نتائج</h4>
            <p class="text-muted">لم يتم العثور على كتب صوتية تطابق معايير البحث.</p>
        </div>
    @endif
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-exclamation-triangle me-2"></i> تأكيد الحذف</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="fs-5">هل أنت متأكد من أنك تريد حذف الكتاب <strong id="bookNameToDelete"></strong>؟</p>
                <small class="text-muted">لا يمكن التراجع عن هذا الإجراء.</small>
            </div>
            <div class="modal-footer border-0 justify-content-center p-3">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">إلغاء</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4">نعم، احذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- 5. أضف الـ JavaScript الخاص بهذه الصفحة فقط --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // View Toggling
    const gridViewBtn = document.getElementById('gridViewBtn');
    const tableViewBtn = document.getElementById('tableViewBtn');
    const gridContainer = document.getElementById('gridContainer');
    const tableContainer = document.getElementById('tableContainer');

    if (gridViewBtn) {
        gridViewBtn.addEventListener('click', () => {
            gridContainer.classList.remove('d-none');
            tableContainer.classList.add('d-none');
            gridViewBtn.classList.add('active', 'btn-light');
            gridViewBtn.classList.remove('btn-outline-light');
            tableViewBtn.classList.remove('active', 'btn-light');
            tableViewBtn.classList.add('btn-outline-light');
        });

        tableViewBtn.addEventListener('click', () => {
            tableContainer.classList.remove('d-none');
            gridContainer.classList.add('d-none');
            tableViewBtn.classList.add('active', 'btn-light');
            tableViewBtn.classList.remove('btn-outline-light');
            gridViewBtn.classList.remove('active', 'btn-light');
            gridViewBtn.classList.add('btn-outline-light');
        });
    }

    // Filtering Logic
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const audioBookItems = document.querySelectorAll('.audio-book-item');
    const visibleCountEl = document.getElementById('visibleCount');
    const noResultsEl = document.getElementById('noResults');

    function filterItems() {
        const searchTerm = searchInput.value.toLowerCase();
        const category = categoryFilter.value;
        const status = statusFilter.value;
        let visibleCount = 0;

        audioBookItems.forEach(item => {
            const title = item.dataset.title;
            const author = item.dataset.author;
            const itemCategory = item.dataset.category;
            const itemStatus = item.dataset.status;

            const matchesSearch = title.includes(searchTerm) || author.includes(searchTerm);
            const matchesCategory = category === '' || itemCategory === category;
            const matchesStatus = status === '' || itemStatus === status;

            if (matchesSearch && matchesCategory && matchesStatus) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        visibleCountEl.textContent = visibleCount;
        noResultsEl.classList.toggle('d-none', visibleCount > 0);
    }

    if (searchInput) {
        searchInput.addEventListener('keyup', filterItems);
        categoryFilter.addEventListener('change', filterItems);
        statusFilter.addEventListener('change', filterItems);

        clearFiltersBtn.addEventListener('click', () => {
            searchInput.value = '';
            categoryFilter.value = '';
            statusFilter.value = '';
            filterItems();
        });
    }
});

// Delete Modal Logic
function showDeleteModal(id, name) {
    const deleteForm = document.getElementById('deleteForm');
    const bookNameToDelete = document.getElementById('bookNameToDelete');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    
    // Dynamically set the form action URL
    deleteForm.action = `/publisher/audio-books/${id}`;
    bookNameToDelete.textContent = `"${name}"`;
    
    deleteModal.show();
}
</script>
@endpush
