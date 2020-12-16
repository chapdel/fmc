<?php

namespace Spatie\Mailcoach\Events;

use Spatie\Mailcoach\Models\CampaignOpen;

class CampaignOpenedEvent
{

    public function __construct(
        public CampaignOpen $campaignOpen
    ) {}
}
