<?php

namespace Spatie\Mailcoach\Domain\Campaign\Events;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class SoftBounceRegisteredEvent
{
    public function __construct(
        public Send $send
    ) {
    }
}
