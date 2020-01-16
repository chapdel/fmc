<?php

namespace Spatie\Mailcoach\Events;

use Spatie\Mailcoach\Models\CampaignOpen;

class CampaignOpenedEvent
{
    public CampaignOpen $campaignOpen;

    public function __construct(CampaignOpen $campaignOpen)
    {
        $this->campaignOpen = $campaignOpen;
    }
}
