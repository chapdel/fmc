<?php

namespace Spatie\Mailcoach\Events;

use Spatie\Mailcoach\Models\CampaignClick;

class CampaignLinkClickedEvent
{
    public CampaignClick $campaignClick;

    public function __construct(CampaignClick $campaignClick)
    {
        $this->campaignClick = $campaignClick;
    }
}
