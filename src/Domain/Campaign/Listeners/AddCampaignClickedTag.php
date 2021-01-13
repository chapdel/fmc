<?php

namespace Spatie\Mailcoach\Domain\Campaign\Listeners;

use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignLinkClickedEvent;
use Spatie\Mailcoach\Domain\Shared\Support\LinkHasher;

class AddCampaignClickedTag
{
    public function handle(CampaignLinkClickedEvent $event)
    {
        $campaign = $event->campaignClick->link->campaign;
        $subscriber = $event->campaignClick->subscriber;

        $hash = LinkHasher::hash($event->campaignClick->link->url);

        $subscriber->addTag("campaign-{$campaign->id}-clicked", TagType::MAILCOACH);
        $subscriber->addTag("campaign-{$campaign->id}-clicked-{$hash}", TagType::MAILCOACH);
    }
}
