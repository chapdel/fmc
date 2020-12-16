<?php

namespace Spatie\Mailcoach\Events;

use Spatie\Mailcoach\Models\Send;

class ComplaintRegisteredEvent
{

    public function __construct(
        public Send $send
    ) {}
}
