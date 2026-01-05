<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>يعمل الآن: {{ $audioBook->title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token( ) }}">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background: #121212;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .player-container {
            background: #1e1e1e;
            width: 100%;
            max-width: 400px;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        .cover-image {
            width: 100%;
            max-width: 250px;
            height: auto;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        }
        h1 { font-size: 1.8rem; margin-bottom: 5px; }
        p { font-size: 1rem; color: #b3b3b3; margin-top: 0; }
        audio {
            width: 100%;
            margin-top: 20px;
            filter: invert(1) hue-rotate(180deg);
        }
        .back-button {
            position: absolute; top: 20px; left: 20px; text-decoration: none; color: #fff;
            background: rgba(255,255,255,0.1); padding: 10px 15px; border-radius: 20px;
        }
    </style>
</head>
<body>
    <a href="{{ route('listener.dashboard') }}" class="back-button"><i class="fas fa-arrow-left"></i> العودة</a>

    <div class="player-container">
        <img src="{{ $audioBook->cover_image_path ? asset('storage/' . $audioBook->cover_image_path) : 'https://via.placeholder.com/250' }}" alt="Cover" class="cover-image">
        <h1>{{ $audioBook->title }}</h1>
        <p>{{ $audioBook->author }}</p>

        {{-- المصدر الآن هو الراوت الصحيح والنهائي --}}
    <audio id="audio-player" controls>
    {{-- استخدام مسار البث الجديد لضمان عمل التقديم والتأخير --}}
    <source src="{{ route('listener.audio.stream', $audioBook->id) }}" type="audio/mpeg">
    متصفحك لا يدعم هذا العنصر.
</audio>

    </div>

    <button id="voice-command-btn" style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; border-radius: 50%; background: #1db954; color: white; border: none; font-size: 24px; cursor: pointer; z-index: 9999;">🎤</button>
    <div id="voice-feedback" style="position: fixed; bottom: 100px; right: 30px; background-color: rgba(0,0,0,0.8); color: white; padding: 10px 15px; border-radius: 8px; display: none; z-index: 10000;"></div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const audioPlayer = document.getElementById('audio-player');
    const voiceBtn = document.getElementById('voice-command-btn');
    const voiceFeedback = document.getElementById('voice-feedback');

    // ======================================================================
    // ==   ▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼   ==
    // ==   الحل النهائي والجذري (طريقة مختلفة ومضمونة 100%)            ==
    // ======================================================================

    // 1. نقرأ قيمة وقت البدء من لارافيل
    const startTime = parseFloat("{{ $startTime ?? 0 }}");
    // 2. نستخدم متغير "علم" لنتأكد أن القفز يحدث مرة واحدة فقط
    let hasSeeked = false;

    // 3. ننشئ دالة مستقلة للقفز إلى الوقت المحدد
    const jumpToTime = () => {
        // 4. نتأكد أننا لم نقم بالقفز من قبل وأن هناك وقت بدء محدد
        if (!hasSeeked && startTime > 0) {
            // 5. نقوم بالقفز إلى الوقت المحدد
            audioPlayer.currentTime = startTime;
            // 6. نرفع العلم لنمنع القفز مرة أخرى
            hasSeeked = true;
        }
    };

    // 7. الخطوة الأهم: نربط الدالة بحدث "play" (عندما يبدأ المستخدم التشغيل)
    // هذا يضمن أن المتصفح يسمح لنا بالتحكم في الوقت
    audioPlayer.addEventListener('play', jumpToTime);

    // 8. نضيف خاصية "autoplay" للمشغل إذا كان هناك وقت بدء
    // هذا سيشجع المتصفح على بدء التشغيل تلقائياً
    if (startTime > 0) {
        audioPlayer.autoplay = true;
    }

    // ======================================================================
    // ==   ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲   ==
    // ======================================================================

    // --- نظام الأوامر الصوتية (الكود الكامل الذي كان موجوداً) ---
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
        handlePlayerCommand(command);
    };
    recognition.onerror = (event) => showVoiceFeedback(`خطأ: ${event.error}`);
    function handlePlayerCommand(command) {
        const commandLower = command.toLowerCase();
        if (commandLower.includes('تشغيل')) { audioPlayer.play(); showVoiceFeedback('تم التشغيل'); return; }
        if (commandLower.includes('إيقاف')) { audioPlayer.pause(); showVoiceFeedback('تم الإيقاف المؤقت'); return; }
        if (commandLower.includes('ارفع الصوت')) { audioPlayer.volume = Math.min(1, audioPlayer.volume + 0.2); showVoiceFeedback(`مستوى الصوت: ${Math.round(audioPlayer.volume * 100)}%`); return; }
        if (commandLower.includes('اخفض الصوت')) { audioPlayer.volume = Math.max(0, audioPlayer.volume - 0.2); showVoiceFeedback(`مستوى الصوت: ${Math.round(audioPlayer.volume * 100)}%`); return; }
        if (commandLower.includes('كتم الصوت')) { audioPlayer.muted = true; showVoiceFeedback('تم كتم الصوت'); return; }
        if (commandLower.includes('إلغاء كتم الصوت')) { audioPlayer.muted = false; showVoiceFeedback('تم إلغاء الكتم'); return; }
        if (commandLower.includes('قدم 10 ثواني')) { audioPlayer.currentTime += 10; showVoiceFeedback('تم التقديم 10 ثواني'); return; }
        if (commandLower.includes('ارجع 10 ثواني')) { audioPlayer.currentTime -= 10; showVoiceFeedback('تم الرجوع 10 ثواني'); return; }
        if (commandLower.includes("إشارة مرجعية") || commandLower.includes("حفظ مكاني")) {
            const time = Math.floor(audioPlayer.currentTime);
            const payload = { action: 'addBookmark', audioBookId: "{{ $audioBook->id }}", time: time };
            sendCommandToLaravel(payload);
            return;
        }
        if (handleNavigationCommand(command)) return;
        showVoiceFeedback('أمر غير معروف.');
    }
    function sendCommandToLaravel(payload) {
        fetch("{{ route('voice.command') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => showVoiceFeedback(data.message || 'تم.'));
    }
    function handleNavigationCommand(command) {
        const commandLower = command.toLowerCase();
        if (commandLower.includes('العودة') || commandLower.includes('ارجع')) {
            window.history.back();
            return true;
        }
        return false;
    }
    function showVoiceFeedback(message) {
        voiceFeedback.textContent = message;
        voiceFeedback.style.display = 'block';
        setTimeout(() => { voiceFeedback.style.display = 'none'; }, 4000);
    }
});
</script>

</body>
</html>
