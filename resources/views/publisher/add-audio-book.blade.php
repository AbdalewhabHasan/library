{{-- ====================================================================== --}}
{{-- ==   صفحة إضافة كتاب صوتي جديد (متعددة الخطوات)                     == --}}
{{-- ==   File: resources/views/publisher/add-audio-book.blade.php       == --}}
{{-- ====================================================================== --}}

{{-- 1. أخبر هذه الصفحة أنها ترث من ليآوت الناشر الموحد --}}
@extends('layouts.publisher')

{{-- 2. حدد عنوان الصفحة --}}
@section('title', 'إضافة كتاب صوتي جديد')

{{-- 3. أضف الـ CSS الخاص بهذه الصفحة فقط --}}
@push('styles')
<style>
    /* هذا الـ CSS خاص فقط بصفحة إضافة الكتاب */
    .progress-bar-container {
        padding: 2rem 2.5rem;
        border-bottom: 1px solid var(--card-border);
        background-color: var(--card-bg);
        border-radius: 1.5rem 1.5rem 0 0;
        display: flex;
    }
    .step { flex: 1; text-align: center; position: relative; }
    .step:not(:last-child)::after { content: ''; position: absolute; top: 50%; left: 50%; transform: translateY(-50%); width: 100%; height: 2px; background-color: var(--card-border); z-index: 1; }
    .step-icon { width: 40px; height: 40px; border-radius: 50%; background-color: var(--card-border); color: var(--text-muted); display: inline-flex; align-items: center; justify-content: center; font-weight: 700; transition: all 0.4s ease; position: relative; z-index: 2; }
    .step-title { font-size: 0.9rem; margin-top: 0.5rem; color: var(--text-muted); transition: all 0.4s ease; }
    .step.active .step-icon { background: var(--primary-gradient); color: white; }
    .step.active .step-title { color: var(--text-color); font-weight: 600; }
    .step.completed .step-icon { background: var(--success-gradient); color: white; }
    .step.completed:not(:last-child)::after { background: var(--success-gradient); }

    .form-container { background: var(--card-bg); border-radius: 0 0 1.5rem 1.5rem; box-shadow: var(--shadow-light); }
    .form-step { display: none; padding: 2.5rem; animation: fadeIn 0.5s; }
    .form-step.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .form-step h2 { font-size: 1.75rem; margin-bottom: 2rem; text-align: center; color: var(--text-color); }
    .form-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; }
    .form-group.full-width { grid-column: 1 / -1; }
    .form-label { display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-color); }
    .form-control, .form-select { background-color: var(--bg-color); color: var(--text-color); border: 1px solid var(--card-border); border-radius: 0.75rem; }
    .form-control:focus, .form-select:focus { background-color: var(--bg-color); color: var(--text-color); border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2); }
    .file-upload-area { border: 2px dashed var(--card-border); border-radius: 1rem; padding: 2rem; text-align: center; cursor: pointer; transition: all 0.3s ease; }
    .file-upload-area:hover { border-color: #667eea; background-color: var(--bg-color); }
    .review-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; align-items: start; }
    .review-details dl { display: grid; grid-template-columns: auto 1fr; gap: 1rem; }
    .review-details dt { font-weight: 700; color: var(--text-color); }
    .review-details dd { color: var(--text-muted); }
    .form-navigation { display: flex; justify-content: space-between; margin-top: 2.5rem; }
    .btn { padding: 0.9rem 2rem; border: none; border-radius: 0.75rem; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-family: 'Cairo', sans-serif; }
    .btn-primary { background: var(--primary-gradient); color: white; }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 7px 20px rgba(0,0,0,0.1); }
    .btn-submit { background: var(--success-gradient); }
</style>
@endpush

{{-- 4. هذا هو المحتوى الخاص بصفحة إضافة الكتاب فقط --}}
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="progress-bar-container">
                <div class="step active" data-step="1"><div class="step-icon"><i class="fas fa-info"></i></div><div class="step-title">المعلومات الأساسية</div></div>
                <div class="step" data-step="2"><div class="step-icon"><i class="fas fa-paperclip"></i></div><div class="step-title">الوصف والملفات</div></div>
                <div class="step" data-step="3"><div class="step-icon"><i class="fas fa-check"></i></div><div class="step-title">المراجعة والتأكيد</div></div>
            </div>

            <div class="form-container">
                <form action="{{ route('publisher.audio-books.store') }}" method="POST" enctype="multipart/form-data" id="audioBookForm">
                    @csrf

                    <!-- Step 1: Basic Information -->
                    <div class="form-step active" data-step="1">
                        <h2>1. المعلومات الأساسية</h2>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="title" class="form-label">عنوان الكتاب</label>
                                <input type="text" name="title" id="title" class="form-control" placeholder="مثال: مغامرات الفضاء" value="{{ old('title') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="author" class="form-label">المؤلف</label>
                                <input type="text" name="author" id="author" class="form-control" placeholder="مثال: أحمد خالد توفيق" value="{{ old('author') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="narrator" class="form-label">الراوي (اختياري)</label>
                                <input type="text" name="narrator" id="narrator" class="form-control" placeholder="مثال: خالد الصاوي" value="{{ old('narrator') }}">
                            </div>
                            <div class="form-group">
                                <label for="duration" class="form-label">المدة (بالدقائق)</label>
                                <input type="number" name="duration" id="duration" class="form-control" placeholder="مثال: 180" value="{{ old('duration') }}" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="category_id" class="form-label">التصنيف</label>
                                <select name="category_id" id="category_id" class="form-select" required>
                                    <option value="" disabled selected>اختر التصنيف</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="language" class="form-label">اللغة</label>
                                <select name="language" id="language" class="form-select" required>
                                    <option value="" disabled selected>اختر اللغة</option>
                                    <option value="العربية" {{ old('language') == 'العربية' ? 'selected' : '' }}>العربية</option>
                                    <option value="الإنجليزية" {{ old('language') == 'الإنجليزية' ? 'selected' : '' }}>الإنجليزية</option>
                                    <option value="الفرنسية" {{ old('language') == 'الفرنسية' ? 'selected' : '' }}>الفرنسية</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-navigation">
                            <a href="{{ route('publisher.dashboard') }}" class="btn btn-secondary">إلغاء</a>
                            <button type="button" class="btn btn-primary btn-next">التالي</button>
                        </div>
                    </div>

                    <!-- Step 2: Description & Files -->
                    <div class="form-step" data-step="2">
                        <h2>2. الوصف والملفات</h2>
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label for="description" class="form-label">وصف الكتاب (اختياري)</label>
                                <textarea name="description" id="description" class="form-control" rows="4" placeholder="أدخل وصفاً جذاباً للكتاب...">{{ old('description') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="cover_image" class="form-label">صورة الغلاف</label>
                                <div class="file-upload-area" id="coverUploadArea">
                                    <input type="file" name="cover_image" id="cover_image" style="display: none;" accept="image/*" required>
                                    <i class="fas fa-image fa-2x" style="color: var(--text-muted); margin-bottom: 1rem;"></i>
                                    <div>اضغط أو اسحب صورة الغلاف هنا</div>
                                </div>
                                <div class="file-preview" id="coverPreview"></div>
                            </div>
                            <div class="form-group">
                                <label for="file" class="form-label">الملف الصوتي</label>
                                <div class="file-upload-area" id="audioUploadArea">
                                    <input type="file" name="file" id="file" style="display: none;" accept="audio/*" required>
                                    <i class="fas fa-music fa-2x" style="color: var(--text-muted); margin-bottom: 1rem;"></i>
                                    <div>اضغط أو اسحب الملف الصوتي هنا</div>
                                </div>
                                <div class="file-preview" id="audioPreview"></div>
                            </div>
                            <div class="form-group">
                                <label for="pdf_file" class="form-label">ملف PDF (اختياري)</label>
                                <div class="file-upload-area" id="pdfUploadArea">
                                    <input type="file" name="pdf_file" id="pdf_file" style="display: none;" accept="application/pdf">
                                    <i class="fas fa-file-pdf fa-2x" style="color: var(--text-muted); margin-bottom: 1rem;"></i>
                                    <div>اضغط أو اسحب ملف PDF هنا</div>
                                </div>
                                <div class="file-preview" id="pdfPreview"></div>
                            </div>
                        </div>
                        <div class="form-navigation">
                            <button type="button" class="btn btn-secondary btn-prev">السابق</button>
                            <button type="button" class="btn btn-primary btn-next">التالي</button>
                        </div>
                    </div>

                    <!-- Step 3: Review & Submit -->
                    <div class="form-step" data-step="3">
                        <h2>3. المراجعة النهائية</h2>
                        <div class="review-grid">
                            <div class="review-cover">
                                <img id="reviewCover" src="https://via.placeholder.com/300x450.png?text=الغلاف" alt="غلاف الكتاب" style="width: 100%; border-radius: 1rem;">
                            </div>
                            <div class="review-details">
                                <h3 id="reviewTitle" style="margin-bottom: 1.5rem;">عنوان الكتاب</h3>
                                <dl>
                                    <dt>المؤلف</dt><dd id="reviewAuthor">-</dd>
                                    <dt>الراوي</dt><dd id="reviewNarrator">-</dd>
                                    <dt>المدة</dt><dd id="reviewDuration">-</dd>
                                    <dt>التصنيف</dt><dd id="reviewCategory">-</dd>
                                    <dt>اللغة</dt><dd id="reviewLanguage">-</dd>
                                    <dt>الملف الصوتي</dt><dd id="reviewAudioFile">-</dd>
                                    <dt>ملف PDF</dt><dd id="reviewPdfFile">-</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="form-navigation">
                            <button type="button" class="btn btn-secondary btn-prev">السابق</button>
                            <button type="submit" class="btn btn-primary btn-submit">تأكيد وإرسال</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- 5. أضف الـ JavaScript الخاص بهذه الصفحة فقط --}}
@push('scripts' )
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('audioBookForm');
    const steps = Array.from(document.querySelectorAll('.form-step'));
    const progressSteps = Array.from(document.querySelectorAll('.progress-bar-container .step'));
    let currentStep = 1;

    function showStep(stepNumber) {
        steps.forEach(step => step.classList.remove('active'));
        document.querySelector(`.form-step[data-step="${stepNumber}"]`).classList.add('active');
        
        progressSteps.forEach((step, index) => {
            step.classList.remove('active', 'completed');
            if (index < stepNumber - 1) {
                step.classList.add('completed');
            }
            if (index === stepNumber - 1) {
                step.classList.add('active');
            }
        });
        currentStep = stepNumber;
    }

    function validateStep(stepNumber) {
        let isValid = true;
        const stepInputs = Array.from(steps[stepNumber - 1].querySelectorAll('input[required], select[required], textarea[required]'));
        
        stepInputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });
        return isValid;
    }

    document.querySelectorAll('.btn-next').forEach(button => {
        button.addEventListener('click', () => {
            if (validateStep(currentStep)) {
                if (currentStep === 2) {
                    updateReviewStep();
                }
                showStep(currentStep + 1);
            }
        });
    });

    document.querySelectorAll('.btn-prev').forEach(button => {
        button.addEventListener('click', () => {
            showStep(currentStep - 1);
        });
    });

    function setupFileUpload(areaId, inputId, previewId) {
        const area = document.getElementById(areaId);
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);

        area.addEventListener('click', () => input.click());
        area.addEventListener('dragover', e => { e.preventDefault(); area.classList.add('dragover'); });
        area.addEventListener('dragleave', () => area.classList.remove('dragover'));
        area.addEventListener('drop', e => {
            e.preventDefault();
            area.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                input.files = e.dataTransfer.files;
                handleFileChange(input, preview);
            }
        });
        input.addEventListener('change', () => handleFileChange(input, preview));
    }

    function handleFileChange(input, preview) {
        const file = input.files[0];
        if (!file) {
            preview.innerHTML = '';
            return;
        }
        
        if (input.accept.includes('image')) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.innerHTML = `<img src="${e.target.result}" alt="معاينة الغلاف" style="max-width: 150px; border-radius: 0.75rem;">`;
            };
            reader.readAsDataURL(file);
        } else if (input.accept.includes('audio')) {
            preview.innerHTML = `<div class="file-info"><i class="fas fa-file-audio"></i> ${file.name}</div>`;
        } else if (input.accept.includes('application/pdf')) {
            preview.innerHTML = `<div class="file-info"><i class="fas fa-file-pdf"></i> ${file.name}</div>`;
        } else {
            preview.innerHTML = `<div class="file-info">${file.name}</div>`;
        }
    }

    setupFileUpload('coverUploadArea', 'cover_image', 'coverPreview');
    setupFileUpload('audioUploadArea', 'file', 'audioPreview');
    setupFileUpload('pdfUploadArea', 'pdf_file', 'pdfPreview');

    function updateReviewStep() {
        document.getElementById('reviewTitle').textContent = document.getElementById('title').value;
        document.getElementById('reviewAuthor').textContent = document.getElementById('author').value;
        document.getElementById('reviewNarrator').textContent = document.getElementById('narrator').value || 'لا يوجد';
        
        const duration = document.getElementById('duration').value;
        document.getElementById('reviewDuration').textContent = `${duration} دقيقة`;
        
        const categorySelect = document.getElementById('category_id');
        document.getElementById('reviewCategory').textContent = categorySelect.options[categorySelect.selectedIndex].text;
        
        const languageSelect = document.getElementById('language');
        document.getElementById('reviewLanguage').textContent = languageSelect.options[languageSelect.selectedIndex].text;

        const audioFile = document.getElementById('file').files[0];
        document.getElementById('reviewAudioFile').textContent = audioFile ? audioFile.name : 'لم يتم الرفع';

    const pdfFile = document.getElementById('pdf_file').files[0];
    document.getElementById('reviewPdfFile').textContent = pdfFile ? pdfFile.name : 'لا يوجد ملف PDF';

        const coverFile = document.getElementById('cover_image').files[0];
        const reviewCoverImg = document.getElementById('reviewCover');
        if (coverFile) {
            const reader = new FileReader();
            reader.onload = e => { reviewCoverImg.src = e.target.result; };
            reader.readAsDataURL(coverFile);
        } else {
            reviewCoverImg.src = 'https://via.placeholder.com/300x450.png?text=الغلاف';
        }
    }

    showStep(1 );
});
</script>
@endpush
