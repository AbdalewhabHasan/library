{{-- ====================================================================== --}}
{{-- ==   ملف إنشاء قائمة تشغيل (النسخة النهائية مع الأوامر الصوتية)     == --}}
{{-- ====================================================================== --}}

@extends('layouts.publisher')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h2 class="mb-0">إنشاء قائمة تشغيل جديدة</h2>
                </div>
                <div class="card-body p-4">

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5>حدثت الأخطاء التالية:</h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('listener.playlists.store') }}" method="POST" enctype="multipart/form-data" id="create-playlist-form">
                        @csrf

                        {{-- حقل اسم قائمة التشغيل --}}
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">اسم قائمة التشغيل <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}"
                                   placeholder="مثال: قائمتي المفضلة، أغاني هادئة..." required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- حقل الوصف (اختياري) --}}
                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">الوصف (اختياري)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3"
                                      placeholder="وصف مختصر لقائمة التشغيل...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- خيار الخصوصية (عامة / خاصة) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">خيارات الخصوصية</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_public" id="publicPlaylist" value="1" {{ old('is_public', 1) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="publicPlaylist">عامة</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_public" id="privatePlaylist" value="0" {{ old('is_public') == 0 ? 'checked' : '' }}>
                                <label class="form-check-label" for="privatePlaylist">خاصة</label>
                            </div>
                        </div>

                        {{-- حقل صورة الغلاف (اختياري) --}}
                        <div class="mb-3">
                            <label for="cover_image" class="form-label fw-bold">صورة الغلاف (اختياري)</label>
                            <input type="file" class="form-control @error('cover_image') is-invalid @enderror" id="cover_image" name="cover_image" accept="image/*">
                        </div>

                        {{-- أزرار الإرسال والإلغاء --}}
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">إنشاء القائمة</button>
                            <a href="{{ route('listener.playlists.index') }}" class="btn btn-outline-secondary btn-lg">إلغاء والعودة</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ▼▼▼ إضافة زر الميكروفون وحاوية الرسائل ▼▼▼ --}}
<button id="voice-command-btn" style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; border-radius: 50%; background: #28a745; color: white; border: none; font-size: 24px; cursor: pointer; z-index: 9999;">🎤</button>
<div id="voice-feedback" style="position: fixed; bottom: 100px; right: 30px; background-color: rgba(0,0,0,0.8); color: white; padding: 10px 15px; border-radius: 8px; display: none; z-index: 10000;"></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const voiceBtn = document.getElementById('voice-command-btn');
    const voiceFeedback = document.getElementById('voice-feedback');
    const nameInput = document.getElementById('name');
    const descriptionInput = document.getElementById('description');
    const form = document.getElementById('create-playlist-form');

    if (!('SpeechRecognition' in window || 'webkitSpeechRecognition' in window)) {
        if(voiceBtn) voiceBtn.style.display = 'none';
        return;
    }

    const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
    recognition.lang = 'ar-SA';
    recognition.interimResults = false;

    voiceBtn.addEventListener('click', () => {
        showVoiceFeedback('استمع الآن...');
        try { recognition.start(); } catch(e) { /* ignore */ }
    });

    recognition.onresult = (event) => {
        const command = event.results[0][0].transcript.trim();
        showVoiceFeedback(`سمعتك تقول: "${command}"`);
        handleCreatePlaylistCommand(command);
    };

    recognition.onerror = (event) => showVoiceFeedback(`خطأ: ${event.error}`);

    function handleCreatePlaylistCommand(command) {
        const commandLower = command.toLowerCase();

        // --- 1. أمر ملء حقل الاسم ---
        if (commandLower.startsWith("املأ الاسم ب") || commandLower.startsWith("اسم القائمة هو")) {
            const playlistName = command.substring(command.indexOf(" ") + 1).replace("ب", "").trim();
            nameInput.value = playlistName;
            showVoiceFeedback(`تم تعيين الاسم إلى: "${playlistName}"`);
            return;
        }

        // --- 2. أمر ملء حقل الوصف ---
        if (commandLower.startsWith("املأ الوصف ب") || commandLower.startsWith("الوصف هو")) {
            const playlistDesc = command.substring(command.indexOf(" ") + 1).replace("ب", "").trim();
            descriptionInput.value = playlistDesc;
            showVoiceFeedback(`تم تعيين الوصف.`);
            return;
        }

        // --- 3. أمر تحديد الخصوصية ---
        if (commandLower.includes("اجعلها خاصة") || commandLower.includes("قائمة خاصة")) {
            document.getElementById('privatePlaylist').checked = true;
            showVoiceFeedback('تم تحديد القائمة كـ "خاصة".');
            return;
        }
        if (commandLower.includes("اجعلها عامة") || commandLower.includes("قائمة عامة")) {
            document.getElementById('publicPlaylist').checked = true;
            showVoiceFeedback('تم تحديد القائمة كـ "عامة".');
            return;
        }

        // --- 4. أمر الحفظ ---
        if (commandLower.includes("احفظ القائمة") || commandLower.includes("إنشاء القائمة")) {
            showVoiceFeedback('جاري إنشاء القائمة...');
            form.submit();
            return;
        }

        // --- 5. أوامر التنقل العامة ---
        if (handleNavigationCommand(command)) return;

        showVoiceFeedback('أمر غير معروف. جرب "املأ الاسم بـ..."، "املأ الوصف بـ..."، أو "احفظ القائمة"');
    }

    // انسخ دالة handleNavigationCommand من أي ملف آخر والصقها هنا
    function handleNavigationCommand(command) {
        // ... نفس كود دالة التنقل من الملفات السابقة
        const commandLower = command.toLowerCase();
        const routes = {
            'لوحة التحكم': 'listener.dashboard',
            'قوائم التشغيل': 'listener.playlists.index',
        };
        for (const keyword in routes) {
            if (commandLower.includes(keyword)) {
                const routeName = routes[keyword];
                showVoiceFeedback(`جاري الانتقال إلى صفحة ${keyword}...`);
                const baseUrl = "{{ url('/') }}";
                let path = routeName.replace(/\./g, '/');
                window.location.href = `${baseUrl}/${path}`;
                return true;
            }
        }
        return false;
    }

    function showVoiceFeedback(message) {
        voiceFeedback.textContent = message;
        voiceFeedback.style.display = 'block';
        setTimeout(() => { voiceFeedback.style.display = 'none'; }, 5000);
    }
});
</script>
@endpush
