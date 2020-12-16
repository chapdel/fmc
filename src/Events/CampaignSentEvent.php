<?php

namespace Spatie\Mailcoach\Events;

use Spatie\Mailcoach\Models\Campaign;

class CampaignSentEvent
{
    public function __construct(
        public Campaign $campaign
    ) {}
}
