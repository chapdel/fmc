<?php

namespace Spatie\Mailcoach\Domain\Campaign\Events;

use Spatie\Mailcoach\Domain\Content\Models\Click;

class CampaignLinkClickedEvent
{
    public function __construct(
        public Click $campaignClick
    ) {
    }
}
