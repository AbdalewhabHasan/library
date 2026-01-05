{{-- ====================================================================== --}}
{{-- ==   ملف تعديل التعليقات (النسخة النهائية الكاملة 100%)              == --}}
{{-- ====================================================================== --}}

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Edit Comment</h2>
                    {{-- زر العودة بتصميم أفضل داخل الهيدر --}}
                    <a href="{{ route('listener.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-tachometer-alt"></i> العودة إلى لوحة التحكم
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('listener.comments.update', $comment->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="comment-textarea" class="form-label">Your Comment:</label>
                            <textarea id="comment-textarea" name="comment" class="form-control @error('comment') is-invalid @enderror" rows="4" required>{{ old('comment', $comment->comment) }}</textarea>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Update Comment</button>
                        <a href="{{ route('listener.comments.show', $comment->audio_book_id) }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- زر الميكروفون وحاوية الرسائل --}}
<button id="voice-command-btn" style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; border-radius: 50%; background: #28a745; color: white; border: none; font-size: 24px; cursor: pointer; z-index: 9999; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;">🎤</button>
<div id="voice-feedback" style="position: fixed; bottom: 100px; right: 30px; background-color: rgba(0,0,0,0.8); color: white; padding: 10px 15px; border-radius: 8px; display: none; z-index: 10000;"></div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const voiceBtn = document.getElementById('voice-command-btn');
    const voiceFeedback = document.getElementById('voice-feedback');
    const commentTextarea = document.getElementById('comment-textarea');

    if (!('SpeechRecognition' in window || 'webkitSpeechRecognition' in window)) {
        if(voiceBtn) voiceBtn.style.display = 'none';
        return;
    }

    const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
    recognition.lang = 'ar-SA';
    recognition.interimResults = false;

    voiceBtn.addEventListener('click', () => {
        showVoiceFeedback('استمع الآن...');
        try {
            recognition.start();
        } catch(e) {
            showVoiceFeedback('التعرف الصوتي مشغول بالفعل.');
        }
    });

    // ▼▼▼ هذه هي النسخة النهائية والصحيحة لدالة onresult ▼▼▼
    recognition.onresult = (event) => {
        const command = event.results[0][0].transcript.trim();
        showVoiceFeedback(`سمعتك تقول: "${command}"`);
        // الدالة الرئيسية هي التي تقرر ما يجب فعله
        handleDictationCommand(command);
    };

    recognition.onerror = (event) => {
        showVoiceFeedback(`خطأ في التعرف: ${event.error}`);
    };

    // الدالة الرئيسية التي تعالج كل الأوامر في هذه الصفحة
    function handleDictationCommand(command) {
        const commandLower = command.toLowerCase();

        // --- 1. أمر الإملاء (الأولوية القصوى) ---
        if (commandLower.startsWith("املأ الحقل ب")) {
            const newText = command.substring("املأ الحقل ب".length).trim();
            commentTextarea.value = newText;
            showVoiceFeedback('تم ملء الحقل بنجاح.');
            return; // تم تنفيذ الأمر
        }

        // --- 2. أمر الحفظ والعودة الجديد ---
        if (commandLower.includes("حفظ التعديل") || commandLower.includes("حفظ وعودة")) {
            showVoiceFeedback('جاري حفظ التعديلات والعودة...');
            const form = document.querySelector('form[action*="/comments/"]');
            if (form) {
                form.submit();
            }
            return; // تم تنفيذ الأمر
        }

        // --- 3. إذا لم يكن أمراً مخصصاً، تحقق من أوامر التنقل العامة ---
        const isNavigationCommand = handleNavigationCommand(command);
        if (isNavigationCommand) {
            return; // تم تنفيذ الأمر
        }

        // --- 4. إذا لم يكن أي مما سبق ---
        showVoiceFeedback('أمر غير معروف. جرب "املأ الحقل بـ..."، "حفظ التعديل"، أو "اذهب إلى..."');
    }

    // الدالة المسؤولة عن أوامر التنقل العامة
    function handleNavigationCommand(command) {
        const commandLower = command.toLowerCase();
        let routeName = null;

        const routes = {
            'لوحة التحكم': 'listener.dashboard',
            'الرئيسية': 'listener.dashboard',
            'قوائم التشغيل': 'listener.playlists.index',
            'قوائمي': 'listener.playlists.index',
            'تحميلاتي': 'listener.downloadedAudio',
            'الملفات المحملة': 'listener.downloadedAudio',
            'اشتراكاتي': 'listener.subscribedPublishers',
            'الناشرون': 'listener.subscribedPublishers',
            'الإشعارات': 'listener.notifications.index',
            'ملفي الشخصي': 'profile.edit',
            'حسابي': 'profile.edit',
        };

        for (const keyword in routes) {
            if (commandLower.includes(keyword)) {
                routeName = routes[keyword];
                break;
            }
        }

        if (routeName) {
            showVoiceFeedback(`جاري الانتقال إلى صفحة ${Object.keys(routes).find(key => routes[key] === routeName)}...`);
            const baseUrl = "{{ url('/') }}";
            // استبدال النقاط بشرطة مائلة لإنشاء المسار
            let path = routeName.replace(/\./g, '/');
            // معالجة الحالات الخاصة التي لا تتبع النمط
            if (path === 'profile/edit') path = 'profile';

            window.location.href = `${baseUrl}/${path}`;
            return true; // تم العثور على أمر تنقل
        }

        return false; // ليس أمر تنقل
    }

    // دالة مساعدة لعرض الرسائل الصوتية
    function showVoiceFeedback(message) {
        voiceFeedback.textContent = message;
        voiceFeedback.style.display = 'block';
        setTimeout(() => { voiceFeedback.style.display = 'none'; }, 5000);
    }
});
</script>
@endpush
