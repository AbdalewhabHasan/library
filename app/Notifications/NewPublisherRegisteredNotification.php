<?php

namespace App\Notifications;

use App\Models\User; // <-- استدعاء مودل المستخدم
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPublisherRegisteredNotification extends Notification
{
    use Queueable;

    // ▼▼▼ هذا هو الجزء الذي أضفناه ▼▼▼
    /**
     * The newly registered publisher.
     *
     * @var \App\Models\User
     */
    protected $publisher;
    // ▲▲▲ انتهى الجزء المضاف ▲▲▲

    /**
     * Create a new notification instance.
     */
    // ▼▼▼ قمنا بتعديل هذه الدالة ▼▼▼
    public function __construct(User $publisher)
    {
        $this->publisher = $publisher;
    }
    // ▲▲▲ انتهى التعديل ▲▲▲

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // ▼▼▼ قمنا بتعديل هذه الدالة ▼▼▼
        // سنرسل الإشعار إلى قاعدة البيانات، ويمكننا إضافة 'mail' إذا أردنا إرسال بريد أيضاً
        return ['database']; 
        // ▲▲▲ انتهى التعديل ▲▲▲
    }

    /**
     * Get the mail representation of the notification.
     * (هذه الدالة لن يتم استخدامها حالياً لأننا لا نرسل بريداً، ولكن من الجيد تركها)
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('ناشر جديد انضم إلى المنصة!')
                    ->line('قام ناشر جديد بالتسجيل في المنصة.')
                    ->line('اسم الناشر: ' . $this->publisher->name)
                    ->action('عرض تفاصيل المستخدم', url('/admin/users/' . $this->publisher->id))
                    ->line('شكراً لك على استخدام تطبيقنا!');
    }

    /**
     * Get the array representation of the notification.
     * (هذه هي الدالة الأهم، حيث تحدد البيانات التي ستُحفظ في قاعدة البيانات)
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // ▼▼▼ قمنا بتعديل هذه الدالة ▼▼▼
        return [
            'message' => 'ناشر جديد انضم للمنصة: ' . $this->publisher->name,
            'link'    => route('admin.users.show', $this->publisher->id), // <-- الرابط الديناميكي
            'user_id' => $this->publisher->id,
        ];
        // ▲▲▲ انتهى التعديل ▲▲▲
    }
}
