<?php

namespace Spatie\Mailcoach\Events;

use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Models\Subscriber;

class UnsubscribedEvent
{
    public function __construct(
        public Subscriber $subscriber,
        public ?Send $send = null
    ) {}
}
