{{-- ====================================================================== --}}
{{-- ==   ملف الناشرين المشترك بهم (النسخة النهائية مع الأوامر الصوتية)  == --}}
{{-- ====================================================================== --}}

@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">Subscribed Publishers</h1>
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
    <form method="GET" action="{{ route('listener.subscribedPublishers') }}" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" id="search-input" class="form-control" placeholder="Search for a publisher..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    @if($publishers->isEmpty())
        <p class="text-center text-muted mt-4">
            No publishers found{{ request('search') ? " for '".request('search')."'" : '' }}.
        </p>
    @else
        <div class="list-group">
            @foreach ($publishers as $publisher)
                {{-- ▼▼▼ إضافة ID فريد لكل عنصر ناشر ▼▼▼ --}}
                <div class="list-group-item d-flex justify-content-between align-items-center" id="publisher-{{ $publisher->id }}">
                    {{-- ▼▼▼ إضافة كلاس "publisher-name" ليسهل استهدافه ▼▼▼ --}}
                    <h3 class="mb-0 publisher-name">{{ $publisher->name }}</h3>
                    <div>
                        <a href="{{ route('listener.publisher.audioBooks', $publisher->id) }}" class="btn btn-info view-books-btn">
                            View Audio Books
                        </a>
                        {{-- ▼▼▼ إضافة زر إلغاء الاشتراك ▼▼▼ --}}
                        <form action="{{ route('unsubscribe', $publisher->id) }}" method="POST" class="d-inline-block unsubscribe-form">
                            @csrf
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to unsubscribe?')">
                                Unsubscribe
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ▼▼▼ إضافة زر الميكروفون وحاوية الرسائل ▼▼▼ --}}
<button id="voice-command-btn" style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; border-radius: 50%; background: #17a2b8; color: white; border: none; font-size: 24px; cursor: pointer; z-index: 9999;">🎤</button>
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
        handlePublisherCommand(command);
    };

    recognition.onerror = (event) => showVoiceFeedback(`خطأ: ${event.error}`);

    function handlePublisherCommand(command) {
        const commandLower = command.toLowerCase();

        // --- 1. أمر البحث ---
        if (commandLower.startsWith("ابحث عن ناشر")) {
            const searchTerm = command.substring("ابحث عن ناشر".length).trim();
            const searchInput = document.getElementById('search-input');
            if (searchInput) {
                searchInput.value = searchTerm;
                showVoiceFeedback(`جاري البحث عن "${searchTerm}"...`);
                searchInput.form.submit();
            }
            return;
        }

        // --- 2. أمر إلغاء الاشتراك ---
        if (commandLower.startsWith("ألغ اشتراكي في") || commandLower.startsWith("الغاء اشتراك")) {
            const publisherName = command.substring(command.indexOf("في") + 2).trim();
            const targetPublisher = findPublisherByName(publisherName);
            if (targetPublisher) {
                const form = targetPublisher.querySelector('.unsubscribe-form');
                if (form && confirm(`هل أنت متأكد أنك تريد إلغاء الاشتراك في "${publisherName}"؟`)) {
                    showVoiceFeedback(`جاري إلغاء الاشتراك...`);
                    fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form)
                    }).then(response => {
                        if (response.ok) {
                            targetPublisher.remove();
                            showVoiceFeedback('تم إلغاء الاشتراك بنجاح.');
                        } else {
                            showVoiceFeedback('فشل إلغاء الاشتراك.');
                        }
                    });
                }
            } else {
                showVoiceFeedback(`لم أجد ناشراً بهذا الاسم: "${publisherName}"`);
            }
            return;
        }

        // --- 3. أمر عرض كتب الناشر ---
        if (commandLower.startsWith("اذهب إلى كتب") || commandLower.startsWith("اعرض كتب")) {
            const publisherName = command.substring(command.indexOf("كتب") + 3).trim();
            const targetPublisher = findPublisherByName(publisherName);
            if (targetPublisher) {
                const viewLink = targetPublisher.querySelector('.view-books-btn');
                if (viewLink) {
                    showVoiceFeedback(`جاري عرض كتب "${publisherName}"...`);
                    window.location.href = viewLink.href;
                }
            } else {
                showVoiceFeedback(`لم أجد ناشراً بهذا الاسم: "${publisherName}"`);
            }
            return;
        }

        // --- 4. أوامر التنقل العامة ---
        if (handleNavigationCommand(command)) return;

        showVoiceFeedback('أمر غير معروف. جرب "ابحث عن..."، "ألغ اشتراكي في..."، أو "اذهب إلى كتب..."');
    }

    function findPublisherByName(name) {
        const allPublishers = document.querySelectorAll('.list-group-item');
        for (let item of allPublishers) {
            const nameElement = item.querySelector('.publisher-name');
            if (nameElement && nameElement.textContent.trim().toLowerCase().includes(name.toLowerCase())) {
                return item;
            }
        }
        return null;
    }

    // انسخ دالة handleNavigationCommand من أي ملف آخر والصقها هنا
    function handleNavigationCommand(command) {
        const commandLower = command.toLowerCase();
        const routes = {
            'لوحة التحكم': 'listener.dashboard',
            'قوائم التشغيل': 'listener.playlists.index',
            'تحميلاتي': 'listener.downloadedAudio',
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
