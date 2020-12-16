<?php

namespace Spatie\Mailcoach\Events;

use Spatie\Mailcoach\Models\CampaignClick;

class CampaignLinkClickedEvent
{
    public function __construct(
        public CampaignClick $campaignClick
    ) {}
}
