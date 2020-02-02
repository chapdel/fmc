<?php

namespace Spatie\Mailcoach\Events;

use Spatie\Mailcoach\Models\Send;

class BounceRegisteredEvent
{
    private Send $send;

    public function __construct(Send $send)
    {
        $this->send = $send;
    }
}
