<?php

namespace Spatie\Mailcoach\Domain\Audience\Events;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class ResubscribedEvent
{
    public function __construct(
        public Subscriber $subscriber,
    ) {
    }
}
