<?php

namespace Spatie\Mailcoach\Domain\Automation\Listeners;

use Spatie\Mailcoach\Domain\Automation\Events\AutomationMailOpenedEvent;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;

class AddAutomationMailOpenedTag
{
    public function handle(AutomationMailOpenedEvent $event)
    {
        $contentItem = $event->automationMailOpen->contentItem;

        if (! $contentItem->add_subscriber_tags) {
            return;
        }

        $subscriber = $event->automationMailOpen->send->subscriber;

        $subscriber->addTag("automation-mail-{$contentItem->model->uuid}-opened", TagType::Mailcoach);
    }
}
