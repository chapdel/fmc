<?php

namespace Spatie\Mailcoach\Domain\Campaign\Listeners;

use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignLinkClickedEvent;
use Spatie\Mailcoach\Domain\Shared\Support\LinkHasher;

class AddCampaignClickedTag
{
    public function handle(CampaignLinkClickedEvent $event)
    {
        $contentItem = $event->campaignClick->link->contentItem;
        $subscriber = $event->campaignClick->subscriber;

        if ($contentItem->add_subscriber_tags) {
            $subscriber->addTag("campaign-{$contentItem->model->uuid}-clicked", TagType::Mailcoach);
        }

        if ($contentItem->add_subscriber_link_tags) {
            $hash = LinkHasher::hash(
                sendable: $contentItem->model,
                url: $event->campaignClick->link->url,
            );

            $subscriber->addTag($hash, TagType::Mailcoach);
        }
    }
}
