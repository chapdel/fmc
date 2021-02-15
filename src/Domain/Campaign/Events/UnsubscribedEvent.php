<?php

namespace Spatie\Mailcoach\Domain\Campaign\Events;

use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class UnsubscribedEvent
{
    public function __construct(
        public Subscriber $subscriber,
        public ?Send $send = null
    ) {
    }
}
