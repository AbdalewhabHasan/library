{{-- ====================================================================== --}}
{{-- ==   ملف الملفات المحملة (النسخة النهائية مع مشغل صوتي)            == --}}
{{-- ====================================================================== --}}

@extends('layouts.publisher')

@section('content')
<div class="container">
    {{-- زر العودة إلى لوحة التحكم --}}
    <div class="text-center mb-4">
        <a href="{{ route('listener.dashboard') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="fas fa-arrow-left me-2"></i>العودة إلى لوحة التحكم
        </a>
    </div>

    <h2 class="text-center mb-4">Downloaded Audio Books</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    <div class="row">
        @forelse($downloads as $download)
            <div class="col-md-4 mb-4" id="download-{{ $download->audioBook->id }}">
                <div class="card shadow-sm h-100">
                    <img src="{{ $download->audioBook->cover_image_path ? asset('storage/' . $download->audioBook->cover_image_path) : asset('images/audio-placeholder.png') }}"
                         class="card-img-top" alt="Audio Book Cover">

                    <div class="card-body">
                        <h5 class="card-title text-truncate audiobook-title">{{ $download->audioBook->title }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($download->audioBook->description, 100) }}</p>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="badge bg-info">{{ $download->audioBook->category->name ?? 'N/A' }}</span>
                            <span class="badge bg-secondary">{{ $download->audioBook->language }}</span>
                        </div>
                        <small class="text-muted d-block">Author: {{ $download->audioBook->author }}</small>
                        <small class="text-muted">Narrator: {{ $download->audioBook->narrator }}</small>
                    </div>

                    {{-- ▼▼▼ تم تعديل الزر ليقوم بالتشغيل بدلاً من التحميل ▼▼▼ --}}
                  {{-- ▼▼▼ هذا هو الكود الجديد والمحسن بالكامل ▼▼▼ --}}
<div class="card-footer bg-white d-flex justify-content-between align-items-center">
    <span class="text-muted small">Downloaded on {{ $download->created_at->toFormattedDateString() }}</span>

    <button type="button" class="btn btn-sm btn-primary play-button"
            data-file="{{ Storage::url($download->audioBook->file_path) }}"
            data-title="{{ $download->audioBook->title }}">
        <i class="fas fa-play"></i> Play
    </button>
</div>

                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center text-muted" >You haven't downloaded any audio books yet.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- ▼▼▼ إضافة مشغل الصوت الصوتي وزر الميكروفون ▼▼▼ --}}
<div id="audioPlayerContainer" style="display: none; position: fixed; bottom: 0; left: 0; width: 100%; background: #f8f9fa; padding: 15px; box-shadow: 0 -2px 10px rgba(0,0,0,0.1); z-index: 1001;">
    <h6 id="nowPlayingTitle" class="text-center mb-2">Now Playing...</h6>
    <audio id="audioPlayer" controls style="width: 100%; max-width: 600px; margin: auto; display: block;"></audio>
</div>

<button id="voice-command-btn" style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; border-radius: 50%; background: #805ad5; color: white; border: none; font-size: 24px; cursor: pointer; z-index: 9999;">🎤</button>
<div id="voice-feedback" style="position: fixed; bottom: 100px; right: 30px; background-color: rgba(0,0,0,0.8); color: white; padding: 10px 15px; border-radius: 8px; display: none; z-index: 10000;"></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const voiceBtn = document.getElementById('voice-command-btn');
    const voiceFeedback = document.getElementById('voice-feedback');
    const audioPlayerContainer = document.getElementById('audioPlayerContainer');
    const audioPlayer = document.getElementById('audioPlayer');
    const nowPlayingTitle = document.getElementById('nowPlayingTitle');

    // --- 1. ربط أزرار التشغيل بالمشغل الصوتي ---
    document.querySelectorAll('.play-button').forEach(button => {
        button.addEventListener('click', () => {
            const fileUrl = button.dataset.file;
            const title = button.dataset.title;

            if (fileUrl) {
                audioPlayer.src = fileUrl;
                nowPlayingTitle.textContent = `Now Playing: ${title}`;
                audioPlayerContainer.style.display = 'block';
                audioPlayer.play();
            }
        });
    });

    // --- 2. نظام الأوامر الصوتية ---
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
        handleDownloadedCommand(command);
    };

    recognition.onerror = (event) => showVoiceFeedback(`خطأ: ${event.error}`);

    function handleDownloadedCommand(command) {
        const commandLower = command.toLowerCase();

        // --- أمر التشغيل الصوتي ---
        if (commandLower.startsWith("شغل كتاب") || commandLower.startsWith("تشغيل كتاب")) {
            const bookTitle = command.substring(command.indexOf("كتاب") + 4).trim();
            const targetBook = findBookByTitle(bookTitle);
            if (targetBook) {
                const playButton = targetBook.querySelector('.play-button');
                if (playButton) {
                    showVoiceFeedback(`جاري تشغيل "${bookTitle}"...`);
                    // محاكاة النقر على زر التشغيل
                    playButton.click();
                }
            } else {
                showVoiceFeedback(`لم أجد كتاباً بهذا الاسم: "${bookTitle}"`);
            }
            return;
        }

        // --- أوامر التنقل العامة ---
        if (handleNavigationCommand(command)) return;

        showVoiceFeedback('أمر غير معروف. جرب "شغل كتاب..."');
    }

    function findBookByTitle(title) {
        const allBooks = document.querySelectorAll('.card');
        for (let card of allBooks) {
            const titleElement = card.querySelector('.audiobook-title');
            if (titleElement && titleElement.textContent.trim().toLowerCase().includes(title.toLowerCase())) {
                return card.closest('.col-md-4');
            }
        }
        return null;
    }

    function handleNavigationCommand(command) {
        const commandLower = command.toLowerCase();
        const routes = {
            'لوحة التحكم': 'listener.dashboard',
            'قوائم التشغيل': 'listener.playlists.index',
            'اشتراكاتي': 'listener.subscribedPublishers',
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
