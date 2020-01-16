<?php

namespace Spatie\Mailcoach\Events;

use Spatie\Mailcoach\Models\Subscriber;

class SubscribedEvent
{
    public Subscriber $subscriber;

    public function __construct(Subscriber $subscriber)
    {
        $this->subscriber = $subscriber;
    }
}
