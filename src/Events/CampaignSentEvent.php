<?php

namespace Spatie\Mailcoach\Events;

use Spatie\Mailcoach\Models\Campaign;

class CampaignSentEvent
{
    public Campaign $campaign;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }
}
