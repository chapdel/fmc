<?php

namespace Spatie\Mailcoach\Events;

use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Models\Subscriber;

class UnsubscribedEvent
{
    public Subscriber $subscriber;

    public ?Send $send;

    public function __construct(Subscriber $subscriber, Send $send = null)
    {
        $this->subscriber = $subscriber;

        $this->send = $send;
    }
}
