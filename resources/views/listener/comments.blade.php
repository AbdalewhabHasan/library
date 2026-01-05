{{-- ====================================================================== --}}
{{-- ==   ملف عرض التعليقات (النسخة النهائية الكاملة مع نظام الإبلاغ)      == --}}
{{-- ====================================================================== --}}

@extends('layouts.app')

{{-- نضيف بعض التنسيقات الخاصة بزر الإبلاغ في هذه الصفحة --}}
@push('styles')
<style>
    .comment-box {
        position: relative;
    }
    .btn-report-comment {
        position: absolute;
        top: 1rem;
        left: 1rem;
        background: none;
        border: none;
        color: #a0aec0; /* لون رمادي خافت */
        font-size: 0.9rem;
        cursor: pointer;
        opacity: 0.6;
        transition: all 0.3s ease;
    }
    .comment-box:hover .btn-report-comment {
        opacity: 1; /* يظهر بوضوح عند التحويم على صندوق التعليق */
    }
    .btn-report-comment:hover {
        color: #e53e3e; /* لون أحمر عند التحويم على الزر نفسه */
        transform: scale(1.1);
    }
</style>
@endpush


@section('content')
<div class="container">
    <h2 class="text-center mb-4">التعليقات على كتاب "{{ $audioBook->title }}"</h2>

    <div class="text-center mb-4">
        <a href="{{ route('listener.dashboard') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left"></i> العودة إلى لوحة التحكم
        </a>
        {{-- زر للعودة إلى صفحة تفاصيل الكتاب --}}
        <a href="{{ route('listener.audiobook.show', $audioBook->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-book"></i> العودة إلى صفحة الكتاب
        </a>
    </div>

    <!-- Display success or warning messages -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @elseif(session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif

    <!-- Display comments -->
    <div class="mb-4">
        <h4>التعليقات:</h4>
        @forelse($audioBook->comments->sortByDesc('created_at') as $comment)
            <div class="comment-box mb-3 p-3 border rounded" id="comment-{{ $comment->id }}">
                <strong>{{ $comment->user ? $comment->user->name : 'Anonymous' }}</strong>
                <p class="mt-2 mb-1 comment-text">{{ $comment->comment }}</p>
                <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>

                @auth
                    {{-- ========================================================== --}}
                    {{-- ▼▼▼ التعديل الأول: زر الإبلاغ الموحد ▼▼▼ --}}
                    {{-- ========================================================== --}}
                    <button type="button" class="btn-report-comment"
                            title="الإبلاغ عن هذا التعليق"
                            onclick="openGlobalReportModal('App\\Models\\Comment', {{ $comment->id }}, 'تعليق المستخدم: {{ e($comment->user->name) }}')">
                        <i class="fas fa-flag"></i> إبلاغ
                    </button>
                    {{-- ========================================================== --}}

                    @if ($comment->listener_id === Auth::id())
                        <div class="mt-2">
                            <a href="{{ route('listener.comments.edit', $comment->id) }}" class="btn btn-sm btn-warning">تعديل</a>
                            <form action="{{ route('listener.comments.destroy', $comment->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد أنك تريد حذف هذا التعليق؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>
        @empty
            <p class="text-center text-muted">لا توجد تعليقات بعد. كن أول من يشارك برأيه!</p>
        @endforelse
    </div>

    <!-- Add a new comment -->
    @auth
        <form action="{{ route('listener.comments.add', $audioBook->id) }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <textarea name="comment" class="form-control" placeholder="أضف تعليقاً..." rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">إرسال التعليق</button>
        </form>
    @endauth

    @guest
        <p>يجب عليك تسجيل الدخول لإضافة تعليق.</p>
    @endguest
</div>

{{-- زر الميكروفون وحاوية الرسائل --}}
<button id="voice-command-btn" style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; border-radius: 50%; background: #667eea; color: white; border: none; font-size: 24px; cursor: pointer; z-index: 9999; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;">🎤</button>
<div id="voice-feedback" style="position: fixed; bottom: 100px; right: 30px; background-color: rgba(0,0,0,0.8); color: white; padding: 10px 15px; border-radius: 8px; display: none; z-index: 10000;"></div>
@endsection


@push('scripts')
<script>
    // ▼▼▼ التعديل الثاني: إضافة الدالة العامة لفتح نافذة الإبلاغ ▼▼▼
    // ملاحظة: هذه الدالة يجب أن تكون موجودة في ملف app.blade.php الرئيسي
    // لكننا نضعها هنا احتياطياً لضمان عمل الصفحة بشكل مستقل
    if (typeof openGlobalReportModal === 'undefined') {
        function openGlobalReportModal(type, id, name) {
            const reportModalElement = document.getElementById('reportModal');
            if (!reportModalElement || typeof bootstrap === 'undefined') {
                console.error('Modal or Bootstrap is not available.');
                alert('حدث خطأ، لا يمكن فتح نافذة الإبلاغ.');
                return;
            }
            const reportModal = new bootstrap.Modal(reportModalElement);
            document.getElementById('reportModalLabel').textContent = `إبلاغ عن: "${name}"`;
            document.getElementById('reportableType').value = type;
            document.getElementById('reportableId').value = id;
            reportModal.show();
        }
    }

    // كل كود الجافاسكريبت الأصلي الخاص بك موجود هنا بدون تغيير
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
            try {
                recognition.start();
            } catch(e) {
                showVoiceFeedback('التعرف الصوتي مشغول بالفعل.');
            }
        });

        recognition.onresult = (event) => {
            const command = event.results[0][0].transcript.trim();
            showVoiceFeedback(`سمعتك تقول: "${command}"`);
            handleCommentCommand(command);
        };

        recognition.onerror = (event) => {
            showVoiceFeedback(`خطأ في التعرف: ${event.error}`);
        };

        function handleCommentCommand(command) {
            const commandLower = command.toLowerCase();

            if (commandLower.startsWith("احذف التعليق الذي يبدأ ب")) {
                const searchText = command.substring("احذف التعليق الذي يبدأ ب".length).trim();
                const targetComment = findCommentByText(searchText);
                if (targetComment) {
                    const commentId = targetComment.id.split('-')[1];
                    if (confirm(`هل أنت متأكد أنك تريد حذف التعليق الذي يبدأ بـ "${searchText}"؟`)) {
                        deleteComment(commentId);
                    }
                } else {
                    showVoiceFeedback(`لم أجد تعليقاً يبدأ بـ "${searchText}"`);
                }
                return;
            }

            if (commandLower.startsWith("عدل التعليق الذي يبدأ ب")) {
                const searchText = command.substring("عدل التعليق الذي يبدأ ب".length).trim();
                const targetComment = findCommentByText(searchText);
                if (targetComment) {
                    const editButton = targetComment.querySelector('a.btn-warning');
                    if (editButton) {
                        window.location.href = editButton.href;
                    } else {
                        showVoiceFeedback('لا تملك صلاحية تعديل هذا التعليق.');
                    }
                } else {
                    showVoiceFeedback(`لم أجد تعليقاً يبدأ بـ "${searchText}"`);
                }
                return;
            }

            if (commandLower.startsWith("اكتب تعليق")) {
                const commentText = command.substring("اكتب تعليق".length).trim();
                const newCommentTextarea = document.querySelector('form[action*="/comments/add"] textarea');
                if (newCommentTextarea) {
                    newCommentTextarea.value = commentText;
                    showVoiceFeedback('تم ملء حقل التعليق الجديد.');
                } else {
                    showVoiceFeedback('لم يتم العثور على حقل لإضافة تعليق جديد.');
                }
                return;
            }

            const isNavigationCommand = handleNavigationCommand(command);
            if (isNavigationCommand) {
                return;
            }

            showVoiceFeedback('أمر غير معروف. جرب "حذف تعليق..."، "تعديل تعليق..."، أو "اذهب إلى..."');
        }

        function handleNavigationCommand(command) {
            const commandLower = command.toLowerCase();
            let routeName = null;

            const routes = {
                'لوحة التحكم': 'listener.dashboard', 'الرئيسية': 'listener.dashboard',
                'قوائم التشغيل': 'listener.playlists.index', 'قوائمي': 'listener.playlists.index',
                'تحميلاتي': 'listener.downloadedAudio', 'الملفات المحملة': 'listener.downloadedAudio',
                'اشتراكاتي': 'listener.subscribedPublishers', 'الناشرون': 'listener.subscribedPublishers',
                'الإشعارات': 'listener.notifications.index',
                'ملفي الشخصي': 'profile.edit', 'حسابي': 'profile.edit',
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
                let path = routeName.replace(/\./g, '/');
                if (path === 'profile/edit') path = 'profile';

                window.location.href = `${baseUrl}/${path}`;
                return true;
            }

            return false;
        }

        function findCommentByText(searchText) {
            const allComments = document.querySelectorAll('.comment-item .comment-text');
            for (let p of allComments) {
                if (p.textContent.trim().toLowerCase().startsWith(searchText.toLowerCase())) {
                    return p.closest('.comment-item');
                }
            }
            return null;
        }

        function deleteComment(commentId) {
            const form = document.querySelector(`#comment-${commentId} form[action*="destroy"]`);
            if (!form) {
                showVoiceFeedback('خطأ: لم يتم العثور على فورم الحذف.');
                return;
            }

            fetch(form.action, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            })
            .then(response => {
                if (response.ok) {
                    const commentElement = document.getElementById(`comment-${commentId}`);
                    commentElement.style.transition = 'opacity 0.5s ease';
                    commentElement.style.opacity = '0';
                    setTimeout(() => commentElement.remove(), 500);
                    showVoiceFeedback('تم حذف التعليق بنجاح.');
                } else {
                    showVoiceFeedback('فشل حذف التعليق. قد لا تملك الصلاحية.');
                }
            })
            .catch(error => {
                showVoiceFeedback('حدث خطأ أثناء محاولة الحذف.');
            });
        }

        function showVoiceFeedback(message) {
            voiceFeedback.textContent = message;
            voiceFeedback.style.display = 'block';
            setTimeout(() => { voiceFeedback.style.display = 'none'; }, 5000);
        }
    });
</script>
@endpush
