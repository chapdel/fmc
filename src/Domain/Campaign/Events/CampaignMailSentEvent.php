<?php

namespace Spatie\Mailcoach\Domain\Campaign\Events;

use Spatie\Mailcoach\Domain\Campaign\Models\Send;

class CampaignMailSentEvent
{

    public function __construct(
        public Send $send
    ) {}
}
