<?php

namespace Spatie\Mailcoach\Events;

use Spatie\Mailcoach\Models\Send;

class ComplaintRegisteredEvent
{
    public Send $send;

    public function __construct(Send $send)
    {
        $this->send = $send;
    }
}
