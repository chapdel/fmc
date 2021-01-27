<?php

namespace Spatie\Mailcoach\Domain\Campaign\Events;

use Spatie\Mailcoach\Domain\Campaign\Models\Send;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;

class UnsubscribedEvent
{
    public function __construct(
        public Subscriber $subscriber,
        public ?Send $send = null
    ) {
    }
}
