<?php

namespace Spatie\Mailcoach\Events;

use Spatie\Mailcoach\Models\Campaign;

class CampaignStatisticsCalculatedEvent
{
    public function __construct(
        public Campaign $campaign
    ) {}
}
