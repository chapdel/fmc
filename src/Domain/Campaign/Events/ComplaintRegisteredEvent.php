<?php

namespace Spatie\Mailcoach\Domain\Campaign\Events;

use Spatie\Mailcoach\Domain\Campaign\Models\Send;

class ComplaintRegisteredEvent
{
    public function __construct(
        public Send $send
    ) {
    }
}
