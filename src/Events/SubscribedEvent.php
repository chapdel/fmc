<?php

namespace Spatie\Mailcoach\Events;

use Spatie\Mailcoach\Models\Subscriber;

class SubscribedEvent
{
    public function __construct(
        public Subscriber $subscriber
    ) {}
}
