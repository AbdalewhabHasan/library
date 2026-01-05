<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
    use Queueable;

    // ▼▼▼ هذا هو الجزء المهم ▼▼▼
    private $notificationData;

    /**
     * Create a new notification instance.
     */
    public function __construct($notificationData)
    {
        $this->notificationData = $notificationData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // نحن نريد فقط حفظه في قاعدة البيانات
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // هذه هي البيانات التي سيتم حفظها في عمود 'data' بصيغة JSON
        return $this->notificationData;
    }
}
