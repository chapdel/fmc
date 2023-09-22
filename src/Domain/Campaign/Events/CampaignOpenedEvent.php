<?php

namespace Spatie\Mailcoach\Domain\Campaign\Events;

use Spatie\Mailcoach\Domain\Content\Models\Open;

class CampaignOpenedEvent
{
    public function __construct(
        public Open $campaignOpen
    ) {
    }
}
