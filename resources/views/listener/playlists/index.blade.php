{{-- ====================================================================== --}}
{{-- ==   ملف عرض قوائم التشغيل (النسخة النهائية مع الأوامر الصوتية)      == --}}
{{-- ====================================================================== --}}

@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center mb-4" style="font-size: 1.8rem;">Your Playlists</h2>
{{-- ====================================================== --}}
{{-- ==   زر العودة إلى لوحة التحكم (لجميع الصفحات)       == --}}
{{-- ====================================================== --}}

<div class="text-center mb-4">
    <a href="{{ route('listener.dashboard') }}" class="btn btn-outline-secondary rounded-pill">
        <i class="fas fa-arrow-left me-2"></i>العودة إلى لوحة التحكم
    </a>
</div>

{{-- ====================================================== --}}

    <!-- Search Form -->
    <form method="GET" action="{{ route('listener.playlists.index') }}" class="mb-4">
        <div class="input-group">
            {{-- ▼▼▼ إضافة ID لحقل البحث ▼▼▼ --}}
            <input type="text" name="search" id="search-input" class="form-control" placeholder="Search by playlist name" value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-search"></i> Search
            </button>
        </div>
    </form>

    @if($playlists->isEmpty())
        <p class="text-center">You have not created any playlists yet.</p>
    @else
        <div class="row">
            @foreach($playlists as $playlist)
            {{-- ▼▼▼ إضافة ID فريد لكل بطاقة ▼▼▼ --}}
            <div class="col-md-4 mb-4" id="playlist-{{ $playlist->id }}">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        {{-- ▼▼▼ إضافة كلاس "playlist-name" ليسهل استهدافه ▼▼▼ --}}
                        <h5 class="card-title playlist-name" style="font-size: 1.2rem;">{{ $playlist->name }}</h5>
                        <p class="card-text text-muted" style="font-size: 1rem;">{{ $playlist->description }}</p>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                        <a href="{{ route('listener.playlists.show', $playlist->id) }}" class="btn btn-sm btn-info" style="font-size: 0.9rem;">
                            View Playlist
                        </a>

                        <form action="{{ route('listener.playlists.destroy', $playlist->id) }}" method="POST" class="d-inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" style="font-size: 0.9rem;" onclick="return confirm('Are you sure you want to delete this playlist?')">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ▼▼▼ إضافة زر الميكروفون وحاوية الرسائل ▼▼▼ --}}
<button id="voice-command-btn" style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; border-radius: 50%; background: #667eea; color: white; border: none; font-size: 24px; cursor: pointer; z-index: 9999; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;">🎤</button>
<div id="voice-feedback" style="position: fixed; bottom: 100px; right: 30px; background-color: rgba(0,0,0,0.8); color: white; padding: 10px 15px; border-radius: 8px; display: none; z-index: 10000;"></div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const voiceBtn = document.getElementById('voice-command-btn');
    const voiceFeedback = document.getElementById('voice-feedback');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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
        handlePlaylistCommand(command);
    };

    recognition.onerror = (event) => {
        showVoiceFeedback(`خطأ في التعرف: ${event.error}`);
    };

    function handlePlaylistCommand(command) {
        const commandLower = command.toLowerCase();

        // --- 1. أمر البحث ---
        if (commandLower.startsWith("ابحث عن")) {
            const searchTerm = command.substring("ابحث عن".length).trim();
            const searchInput = document.getElementById('search-input');
            if (searchInput) {
                searchInput.value = searchTerm;
                showVoiceFeedback(`جاري البحث عن "${searchTerm}"...`);
                searchInput.form.submit();
            }
            return;
        }

        // --- 2. أمر الحذف ---
        if (commandLower.startsWith("احذف قائمة")) {
            const playlistName = command.substring("احذف قائمة".length).trim();
            const targetPlaylist = findPlaylistByName(playlistName);
            if (targetPlaylist) {
                const form = targetPlaylist.querySelector('form[action*="destroy"]');
                if (form && confirm(`هل أنت متأكد أنك تريد حذف قائمة "${playlistName}"؟`)) {
                    showVoiceFeedback(`جاري حذف القائمة...`);
                    // إرسال طلب الحذف ديناميكياً
                    fetch(form.action, {
                        method: 'POST', // HTML forms use POST for DELETE with method spoofing
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: new FormData(form) // Send form data including _method=DELETE
                    }).then(response => {
                        if (response.ok) {
                            targetPlaylist.remove();
                            showVoiceFeedback('تم حذف القائمة بنجاح.');
                        } else {
                            showVoiceFeedback('فشل حذف القائمة.');
                        }
                    });
                }
            } else {
                showVoiceFeedback(`لم أجد قائمة باسم "${playlistName}"`);
            }
            return;
        }

        // --- 3. أمر الانتقال ---
        if (commandLower.startsWith("اذهب إلى قائمة") || commandLower.startsWith("افتح قائمة")) {
            const playlistName = command.substring(command.indexOf("قائمة") + "قائمة".length).trim();
            const targetPlaylist = findPlaylistByName(playlistName);
            if (targetPlaylist) {
                const viewLink = targetPlaylist.querySelector('a.btn-info');
                if (viewLink) {
                    showVoiceFeedback(`جاري فتح قائمة "${playlistName}"...`);
                    window.location.href = viewLink.href;
                }
            } else {
                showVoiceFeedback(`لم أجد قائمة باسم "${playlistName}"`);
            }
            return;
        }

        // --- 4. أوامر التنقل العامة ---
        if (handleNavigationCommand(command)) {
            return;
        }

        showVoiceFeedback('أمر غير معروف. جرب "ابحث عن..."، "احذف قائمة..."، أو "اذهب إلى قائمة..."');
    }

    function findPlaylistByName(name) {
        const allPlaylists = document.querySelectorAll('.card');
        for (let card of allPlaylists) {
            const titleElement = card.querySelector('.playlist-name');
            if (titleElement && titleElement.textContent.trim().toLowerCase().includes(name.toLowerCase())) {
                return card.closest('.col-md-4'); // Return the main column container
            }
        }
        return null;
    }

    // انسخ دالة handleNavigationCommand من أي ملف آخر والصقها هنا
    function handleNavigationCommand(command) {
        // ... نفس كود دالة التنقل من الملفات السابقة
        const commandLower = command.toLowerCase();
        const routes = {
            'لوحة التحكم': 'listener.dashboard',
            'الرئيسية': 'listener.dashboard',
            'تحميلاتي': 'listener.downloadedAudio',
            'اشتراكاتي': 'listener.subscribedPublishers',
            'الإشعارات': 'listener.notifications.index',
            'ملفي الشخصي': 'profile.edit',
        };
        for (const keyword in routes) {
            if (commandLower.includes(keyword)) {
                const routeName = routes[keyword];
                showVoiceFeedback(`جاري الانتقال إلى صفحة ${keyword}...`);
                const baseUrl = "{{ url('/') }}";
                let path = routeName.replace(/\./g, '/');
                if (path === 'profile/edit') path = 'profile';
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
