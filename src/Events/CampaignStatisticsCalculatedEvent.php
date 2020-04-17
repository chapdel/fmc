<?php

namespace Spatie\Mailcoach\Events;

use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;

class CampaignStatisticsCalculatedEvent
{
    public CampaignConcern $campaign;

    public function __construct(CampaignConcern $campaign)
    {
        $this->campaign = $campaign;
    }
}
