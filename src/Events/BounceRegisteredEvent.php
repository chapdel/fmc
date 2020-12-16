<?php

namespace Spatie\Mailcoach\Events;

use Spatie\Mailcoach\Models\Send;

class BounceRegisteredEvent
{
    public function __construct(
        public Send $send
    ) {}
}
