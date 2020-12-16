<?php

namespace Spatie\Mailcoach\Domain\Campaign\Events;

use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;

class UnconfirmedSubscriberCreatedEvent
{

    public function __construct(
        public Subscriber $subscriber
    ) {}
}
