<?php

namespace Spatie\Mailcoach\Domain\Campaign\Listeners;

use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignOpenedEvent;

class AddCampaignOpenedTag
{
    public function handle(CampaignOpenedEvent $event)
    {
        $contentItem = $event->campaignOpen->contentItem;

        if (! $contentItem->add_subscriber_tags) {
            return;
        }

        $subscriber = $event->campaignOpen->subscriber;
        $subscriber->addTag("campaign-{$contentItem->model->uuid}-opened", TagType::Mailcoach);
    }
}
