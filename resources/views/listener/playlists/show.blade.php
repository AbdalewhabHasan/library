{{-- ====================================================================== --}}
{{-- ==   ملف عرض قائمة تشغيل (النسخة النهائية مع الأوامر الصوتية)       == --}}
{{-- ====================================================================== --}}

@extends('layouts.app')

@section('content')
<div class="container">
    {{-- ====================================================== --}}
{{-- ==   زر العودة إلى لوحة التحكم (لجميع الصفحات)       == --}}
{{-- ====================================================== --}}

<div class="text-center mb-4">
    <a href="{{ route('listener.dashboard') }}" class="btn btn-outline-secondary rounded-pill">
        <i class="fas fa-arrow-left me-2"></i>العودة إلى لوحة التحكم
    </a>
</div>

{{-- ====================================================== --}}

    <h2 class="text-center mb-4" style="font-size: 1.8rem;">Playlist: {{ $playlist->name }}</h2>
    <p class="text-center mb-4" style="font-size: 1.2rem;">{{ $playlist->description }}</p>

    <h3 class="mb-4" style="font-size: 1.6rem;">Audio Books in this Playlist</h3>
    <div class="row">
        @forelse($playlist->audioBooks as $audioBook)
        {{-- ▼▼▼ إضافة ID فريد لكل بطاقة كتاب ▼▼▼ --}}
        <div class="col-md-4 mb-4" id="audiobook-card-{{ $audioBook->id }}">
            <div class="card h-100 shadow-sm">
                <img src="{{ $audioBook->cover_image_path ? asset('storage/' . $audioBook->cover_image_path) : asset('images/audio-placeholder.png') }}"
                     class="card-img-top" alt="Audio Book Cover" style="height: 200px; object-fit: cover;">

                <div class="card-body">
                    {{-- ▼▼▼ إضافة كلاس "audiobook-title" ليسهل استهدافه ▼▼▼ --}}
                    <h5 class="card-title text-truncate audiobook-title" style="font-size: 1.2rem;">{{ $audioBook->title }}</h5>
                    <p class="card-text text-muted" style="font-size: 1rem;">{{ Str::limit($audioBook->description, 100) }}</p>

                    {{-- Rating Section --}}
                    <div class="mt-3">
                        <div class="rating-stars" data-audiobook-id="{{ $audioBook->id }}">
                            @php
                                $averageRating = $audioBook->ratings->avg('rating');
                                $userRating = $audioBook->ratings->where('listener_id', Auth::id())->first();
                                $currentRating = $userRating ? $userRating->rating : 0;
                            @endphp
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="fa fa-star {{ $i <= $currentRating ? 'text-warning' : 'text-muted' }}" data-rating="{{ $i }}" style="cursor: pointer;"></span>
                            @endfor
                        </div>
                        <small class="text-muted">{{ number_format($averageRating, 1) }} / 5</small>
                    </div>

                    {{-- Remove Rating Container --}}
                    <div class="remove-rating-container mt-2">
                        @if ($userRating)
                            <button class="btn btn-sm btn-danger remove-rating-btn" data-audiobook-id="{{ $audioBook->id }}">Remove Rating</button>
                        @endif
                    </div>
                </div>

                <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-sm btn-primary play-button" data-file="{{ Storage::url($audioBook->file_path) }}" data-title="{{ $audioBook->title }}" data-audiobook-id="{{ $audioBook->id }}">
                        <i class="fas fa-play"></i> Play
                    </button>

                    <form action="{{ route('listener.playlist.removeAudioBook', ['playlist' => $playlist->id, 'audioBook' => $audioBook->id]) }}" method="POST" class="d-inline-block remove-book-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
            <div class="col-12">
                <p class="text-center text-muted">This playlist is empty.</p>
            </div>
        @endforelse
    </div>
     <div class="text-center mt-4">
        <a href="{{ route('listener.playlists.index') }}" class="btn btn-secondary">Back to all playlists</a>
    </div>
</div>

{{-- Audio Player & Voice Command UI --}}
<div id="audioPlayerContainer" style="display: none; position: fixed; bottom: 0; left: 0; width: 100%; background: #f8f9fa; padding: 15px; box-shadow: 0 -2px 10px rgba(0,0,0,0.1); z-index: 1001;">
    <h6 id="nowPlayingTitle" class="text-center mb-2">Now Playing...</h6>
    <audio id="audioPlayer" controls style="width: 100%; max-width: 600px; margin: auto; display: block;"></audio>
</div>
<button id="voice-command-btn" style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; border-radius: 50%; background: #667eea; color: white; border: none; font-size: 24px; cursor: pointer; z-index: 1002;">🎤</button>
<div id="voice-feedback" style="position: fixed; bottom: 100px; right: 30px; background-color: rgba(0,0,0,0.8); color: white; padding: 10px 15px; border-radius: 8px; display: none; z-index: 10000;"></div>
@endsection

@push('scripts')
<script>
{{-- ====================================================================== --}}
{{-- ==   السكربت الكامل والجديد لهذه الصفحة (ديناميكي + صوتي)          == --}}
{{-- ====================================================================== --}}
document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const audioPlayerContainer = document.getElementById('audioPlayerContainer');
    const audioPlayer = document.getElementById('audioPlayer');
    const nowPlayingTitle = document.getElementById('nowPlayingTitle');
    const voiceBtn = document.getElementById('voice-command-btn');
    const voiceFeedback = document.getElementById('voice-feedback');

    // --- 1. نظام التشغيل والتقييم الديناميكي ---
    function initializeDynamicButtons() {
        // Play Buttons
        document.querySelectorAll('.play-button').forEach(button => {
            button.addEventListener('click', () => {
                audioPlayer.src = button.dataset.file;
                nowPlayingTitle.textContent = `Now Playing: ${button.dataset.title}`;
                audioPlayerContainer.style.display = 'block';
                audioPlayer.play();
            });
        });

        // Rating Stars
        document.querySelectorAll('.rating-stars').forEach(container => {
            container.addEventListener('click', (event) => {
                if (event.target.classList.contains('fa-star')) {
                    submitRating(container.dataset.audiobookId, event.target.dataset.rating);
                }
            });
        });

        // Remove Rating Buttons
        document.body.addEventListener('click', (event) => {
            if (event.target.matches('.remove-rating-btn')) {
                event.preventDefault();
                removeRating(event.target.dataset.audiobookId);
            }
        });
    }

    function submitRating(audiobookId, rating) {
        fetch(`/listener/rate/${audiobookId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ rating: rating }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateRatingUI(audiobookId, rating);
                showNotification('Rating submitted successfully!');
            }
        });
    }

    function removeRating(audiobookId) {
        if (!confirm('Are you sure?')) return;
        fetch(`/listener/audio-books/${audiobookId}/remove-rating`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken },
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateRatingUI(audiobookId, 0);
                showNotification('Rating removed successfully!');
            }
        });
    }

    function updateRatingUI(audiobookId, rating) {
        const card = document.getElementById(`audiobook-card-${audiobookId}`);
        if (!card) return;
        const starsContainer = card.querySelector('.rating-stars');
        const removeContainer = card.querySelector('.remove-rating-container');
        starsContainer.querySelectorAll('.fa-star').forEach(star => {
            star.classList.toggle('text-warning', star.dataset.rating <= rating);
            star.classList.toggle('text-muted', star.dataset.rating > rating);
        });
        removeContainer.innerHTML = (rating > 0) ? `<button class="btn btn-sm btn-danger remove-rating-btn" data-audiobook-id="${audiobookId}">Remove Rating</button>` : '';
    }

    // --- 2. نظام الأوامر الصوتية ---
    if ('SpeechRecognition' in window || 'webkitSpeechRecognition' in window) {
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
            handlePlaylistBookCommand(command);
        };

        recognition.onerror = (event) => showVoiceFeedback(`خطأ: ${event.error}`);

        function handlePlaylistBookCommand(command) {
            const commandLower = command.toLowerCase();

            // أمر التشغيل
            if (commandLower.startsWith("شغل الكتاب الذي اسمه") || commandLower.startsWith("تشغيل كتاب")) {
                const bookTitle = command.substring(command.indexOf("كتاب") + 4).trim();
                const targetBook = findBookByTitle(bookTitle);
                if (targetBook) {
                    const playButton = targetBook.querySelector('.play-button');
                    if (playButton) {
                        showVoiceFeedback(`جاري تشغيل "${bookTitle}"...`);
                        playButton.click();
                    }
                } else {
                    showVoiceFeedback(`لم أجد كتاباً بهذا الاسم: "${bookTitle}"`);
                }
                return;
            }

            // أمر الحذف
            if (commandLower.startsWith("احذف الكتاب الذي اسمه") || commandLower.startsWith("حذف كتاب")) {
                const bookTitle = command.substring(command.indexOf("كتاب") + 4).trim();
                const targetBook = findBookByTitle(bookTitle);
                if (targetBook) {
                    const form = targetBook.querySelector('.remove-book-form');
                    if (form && confirm(`هل أنت متأكد أنك تريد حذف "${bookTitle}" من القائمة؟`)) {
                        showVoiceFeedback(`جاري حذف الكتاب...`);
                        fetch(form.action, {
                            method: 'POST',
                            body: new FormData(form)
                        }).then(response => {
                            if (response.ok) {
                                targetBook.remove();
                                showVoiceFeedback('تم حذف الكتاب بنجاح.');
                            } else {
                                showVoiceFeedback('فشل حذف الكتاب.');
                            }
                        });
                    }
                } else {
                    showVoiceFeedback(`لم أجد كتاباً بهذا الاسم: "${bookTitle}"`);
                }
                return;
            }

            // أوامر التنقل العامة
            if (handleNavigationCommand(command)) return;

            showVoiceFeedback('أمر غير معروف. جرب "شغل كتاب..." أو "احذف كتاب..."');
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
    }

    // --- 3. الدوال المساعدة (التنقل والإشعارات) ---
    function handleNavigationCommand(command) {
        // ... نفس كود دالة التنقل من الملفات السابقة
        const commandLower = command.toLowerCase();
        const routes = {
            'لوحة التحكم': 'listener.dashboard',
            'الرئيسية': 'listener.dashboard',
            'قوائم التشغيل': 'listener.playlists.index',
            'تحميلاتي': 'listener.downloadedAudio',
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

    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.style.cssText = "position:fixed; top:20px; right:20px; padding:15px; background-color: #28a745; color:white; border-radius:5px; z-index:10001;";
        if(type === 'error') notification.style.backgroundColor = '#dc3545';
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 4000);
    }

    function showVoiceFeedback(message) {
        voiceFeedback.textContent = message;
        voiceFeedback.style.display = 'block';
        setTimeout(() => { voiceFeedback.style.display = 'none'; }, 5000);
    }

    // --- بدء تشغيل كل شيء ---
    initializeDynamicButtons();
});
</script>
@endpush
