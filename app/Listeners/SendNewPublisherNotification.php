<?php

namespace App\Listeners;

use App\Events\PublisherRegistered;
use App\Models\User; // <-- استدعاء مودل المستخدم
use App\Notifications\NewPublisherRegisteredNotification; // <-- استدعاء كلاس الإشعار الذي سننشئه بعد قليل
use Illuminate\Support\Facades\Notification; // <-- استدعاء واجهة الإشعارات

class SendNewPublisherNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PublisherRegistered $event): void
    {
        // ▼▼▼ هذا هو الكود الذي أضفناه ▼▼▼

        // 1. ابحث عن كل المستخدمين الذين لديهم دور "admin"
        $admins = User::where('role', 'admin')->get();

        // 2. قم بإرسال إشعار لكل أدمن موجود
        if ($admins->isNotEmpty()) {
            // استخراج الناشر الجديد من الحدث
            $publisher = $event->publisher;
            
            // إرسال الإشعار للأدمنز
            Notification::send($admins, new NewPublisherRegisteredNotification($publisher));
        }
        
        // ▲▲▲ انتهى الكود المضاف ▲▲▲
    }
}
