<?php

namespace App\Events;

use App\Models\User; // <-- قمنا باستدعاء مودل المستخدم
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PublisherRegistered
{
    use Dispatchable, SerializesModels;

    // ▼▼▼ هذا هو الجزء الذي أضفناه ▼▼▼
    /**
     * The newly registered publisher.
     *
     * @var \App\Models\User
     */
    public $publisher;
    // ▲▲▲ انتهى الجزء المضاف ▲▲▲

    /**
     * Create a new event instance.
     *
     * @param \App\Models\User $publisher
     * @return void
     */
    // ▼▼▼ قمنا بتعديل هذه الدالة ▼▼▼
    public function __construct(User $publisher)
    {
        $this->publisher = $publisher;
    }
    // ▲▲▲ انتهى التعديل ▲▲▲
}
