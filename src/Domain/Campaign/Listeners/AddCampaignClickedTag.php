<?php

namespace Spatie\Mailcoach\Domain\Campaign\Listeners;

use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignLinkClickedEvent;

class AddCampaignClickedTag
{
    public function handle(CampaignLinkClickedEvent $event)
    {
        $campaign = $event->campaignClick->link->campaign;
        $subscriber = $event->campaignClick->subscriber;

        $subscriber->addTag("campaign-{$campaign->id}-clicked", TagType::MAILCOACH);
    }
}
