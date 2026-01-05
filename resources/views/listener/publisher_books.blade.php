@extends('layouts.publisher')

@section('content')
<div class="container mt-5" style="padding-bottom: 100px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">كتب الناشر: {{ $publisher->name }}</h2>

        {{-- زر الاشتراك أو إلغاء الاشتراك --}}
        @if(Auth::user()->subscriptions()->where('publisher_id', $publisher->id)->exists())
            <form action="{{ route('unsubscribe', $publisher->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger">إلغاء الاشتراك</button>
            </form>
        @else
            <form action="{{ route('subscribe', $publisher->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">اشتراك</button>
            </form>
        @endif
    </div>

    @if($audiobooks->isEmpty())
        <div class="alert alert-info text-center">لا توجد كتب صوتية متاحة لهذا الناشر حالياً.</div>
    @else
        <div class="row">
            @foreach($audiobooks as $audioBook)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                  {{-- ▼▼▼ الكود المحدث للصورة (بدون رابط) ▼▼▼ --}}
<img src="{{ $audioBook->cover_image_path ? asset('storage/' . $audioBook->cover_image_path) : 'https://via.placeholder.com/300x200.png?text=AudioBook' }}"
     class="card-img-top" alt="غلاف الكتاب"
     style="height: 200px; object-fit: cover;">

                    <div class="card-body d-flex flex-column">
                        {{-- ▼▼▼ أضفنا data-title هنا ▼▼▼ --}}
                        <h5 class="card-title text-truncate" data-title="{{ $audioBook->title }}">{{ $audioBook->title }}</h5>
                        <p class="card-text text-muted small flex-grow-1">بواسطة: {{ $audioBook->author }}</p>
                        <div class="mt-auto">
                            <button type="button" class="btn btn-sm btn-primary play-button w-100"
                                    data-file="{{ Storage::url($audioBook->file_path  ) }}">
                                <i class="fas fa-play"></i> تشغيل
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- مشغل الصوت المخفي الذي سيظهر عند التشغيل --}}
        <div id="audioPlayerContainer" class="fixed-bottom bg-light p-3 shadow-lg" style="display: none;">
            {{-- ▼▼▼ أضفنا ID هنا لعرض العنوان ▼▼▼ --}}
            <h6 id="nowPlayingTitle" class="mb-2">يعمل الآن:</h6>
            <audio id="audio-player" controls class="w-100">
                <source src="" type="audio/mpeg">
                متصفحك لا يدعم تشغيل الصوت.
            </audio>
        </div>
    @endif

    <div class="d-flex justify-content-center mt-4">
        {{ $audiobooks->links() }}
    </div>
</div>

{{-- ▼▼▼ زر الميكروفون وحاوية الرسائل ▼▼▼ --}}

{{-- ▼▼▼ الكود الجديد لزر الميكروفون (تصميم ومكان جديد) ▼▼▼ --}}
<button id="voice-command-btn" style="
    position: fixed;
    bottom: 90px; /* <--- تم رفعه للأعلى */
    right: 30px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(145deg, #5c67e3, #8f6ed5); /* <--- تصميم أجمل */
    color: white;
    border: none;
    font-size: 24px;
    cursor: pointer;
    z-index: 9999; /* <--- رقم عالي جداً لضمان ظهوره في الأعلى */
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2); /* <--- ظل أوضح */
    transition: all 0.3s ease; /* <--- حركة ناعمة */
">🎤</button>

{{-- حاوية الرسائل (تم رفعها أيضاً) --}}
<div id="voice-feedback" style="
    position: fixed;
    bottom: 160px; /* <--- تم رفعه ليكون فوق الزر */
    right: 30px;
    background-color: rgba(0,0,0,0.8);
    color: white;
    padding: 10px 15px;
    border-radius: 8px;
    display: none;
    z-index: 10000; /* <--- أعلى من كل شيء */
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
"></div>


@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const playButtons = document.querySelectorAll('.play-button');
        const audioPlayerContainer = document.getElementById('audioPlayerContainer');
        const audioPlayer = document.getElementById('audio-player');
        const nowPlayingTitle = document.getElementById('nowPlayingTitle');

        playButtons.forEach(button => {
            button.addEventListener('click', () => {
                const fileUrl = button.getAttribute('data-file');
                const cardBody = button.closest('.card-body');
                const titleElement = cardBody.querySelector('.card-title');
                const title = titleElement.getAttribute('data-title');

                if (fileUrl) {
                    nowPlayingTitle.textContent = `يعمل الآن: ${title}`;
                    const source = audioPlayer.getElementsByTagName('source')[0];
                    source.src = fileUrl;
                    audioPlayerContainer.style.display = 'block';
                    audioPlayer.load();

                    const playPromise = audioPlayer.play();
                    if (playPromise !== undefined) {
                        playPromise.then(_ => {
                            audioPlayer.pause();
                        }).catch(error => {
                            console.error("Autoplay unlock failed:", error);
                        });
                    }
                }
            });
        });

        // --- بداية كود الأوامر الصوتية ---
        const voiceBtn = document.getElementById('voice-command-btn');
        const voiceFeedback = document.getElementById('voice-feedback');

        window.SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!window.SpeechRecognition) {
            voiceBtn.style.display = 'none';
            return;
        }

        const recognition = new SpeechRecognition();
        recognition.lang = 'ar-SA';
        recognition.interimResults = false;

        voiceBtn.addEventListener('click', () => {
            voiceFeedback.textContent = 'استمع الآن...';
            voiceFeedback.style.display = 'block';
            voiceBtn.style.backgroundColor = '#dc3545';
            recognition.start();
        });

        recognition.onresult = (event) => {
            const command = event.results[0][0].transcript.trim().replace('.', '');
            voiceFeedback.textContent = `سمعتك تقول: "${command}"`;

            // إذا لم يتم التعامل مع الأمر محلياً، أرسله إلى الخادم
            if (!handleLocalCommand(command)) {
                sendCommandToLaravel(command);
            }
        };

        recognition.onspeechend = () => {
            recognition.stop();
            voiceBtn.style.backgroundColor = '#007bff';
            setTimeout(() => { voiceFeedback.style.display = 'none'; }, 3000);
        };

        // ▼▼▼ الدالة التي تم إصلاحها بشكل نهائي ▼▼▼
        function handleLocalCommand(command) {
            const player = document.getElementById('audio-player');

            // الشرط الجديد: هل الأمر "تشغيل" أو "إيقاف"؟
            const isPlayCommand = command.includes('تشغيل') || command.includes('شغل');
            const isPauseCommand = command.includes('إيقاف مؤقت') || command.includes('أوقف');

            // إذا لم يكن أياً منهما، أرجع false فوراً
            if (!isPlayCommand && !isPauseCommand) {
                return false;
            }

            // إذا كان الأمر "تشغيل" أو "إيقاف"، تحقق من وجود المشغل
            if (!player || !player.src || player.src.endsWith('/')) {
                voiceFeedback.textContent = 'الرجاء اختيار كتاب أولاً';
                return true; // <--- مهم: أرجع true لمنع الإرسال للخادم
            }

            if (isPlayCommand) {
                player.play().catch(e => console.error("Play command failed:", e));
                voiceFeedback.textContent = 'تم التشغيل';
            } else if (isPauseCommand) {
                player.pause();
                voiceFeedback.textContent = 'تم الإيقاف المؤقت';
            }

            // بما أننا تعاملنا مع الأمر، أرجع true دائماً
            return true;
        }

        function sendCommandToLaravel(command) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            if (!csrfToken) {
                voiceFeedback.textContent = 'خطأ في الأمان (CSRF)';
                return;
            }
            fetch("{{ route('voice.command') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ command: command })
            })
            .then(response => response.json())
            .then(data => {
                if (data.action === 'redirect') {
                    voiceFeedback.textContent = 'جاري الانتقال...';
                    setTimeout(() => { window.location.href = data.url; }, 1000);
                } else if (data.action === 'error') {
                    voiceFeedback.textContent = data.message;
                }
            });
        }
    });
</script>
@endpush
