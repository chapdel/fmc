<?php

namespace Spatie\Mailcoach\Events;

use Spatie\Mailcoach\Models\Subscriber;

class UnconfirmedSubscriberCreatedEvent
{

    public function __construct(
        public Subscriber $subscriber
    ) {}
}
