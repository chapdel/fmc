<?php

namespace Spatie\Mailcoach\Domain\Automation\Listeners;

use Spatie\Mailcoach\Domain\Automation\Events\AutomationMailLinkClickedEvent;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Shared\Support\LinkHasher;

class AddAutomationMailClickedTag
{
    public function handle(AutomationMailLinkClickedEvent $event)
    {
        $contentItem = $event->automationMailClick->link->contentItem;
        $subscriber = $event->automationMailClick->send->subscriber;

        if ($contentItem->add_subscriber_tags) {
            $subscriber->addTag("automation-mail-{$contentItem->model->uuid}-clicked", TagType::Mailcoach);
        }

        if ($contentItem->add_subscriber_link_tags) {
            $hash = LinkHasher::hash(
                sendable: $contentItem->model,
                url: $event->automationMailClick->link->url,
            );

            $subscriber->addTag($hash, TagType::Mailcoach);
        }
    }
}
