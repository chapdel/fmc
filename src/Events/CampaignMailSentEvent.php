<?php

namespace Spatie\Mailcoach\Events;

use Spatie\Mailcoach\Models\Send;

class CampaignMailSentEvent
{

    public function __construct(
        public Send $send
    ) {}
}
