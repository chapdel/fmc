<?php

namespace Spatie\Mailcoach\Domain\Automation\Listeners;

use Spatie\Mailcoach\Domain\Automation\Events\AutomationMailOpenedEvent;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;

class AddAutomationMailOpenedTag
{
    public function handle(AutomationMailOpenedEvent $event)
    {
        $campaign = $event->automationMailOpen->automationMail;
        $subscriber = $event->automationMailOpen->subscriber;

        $subscriber->addTag("automation-mail-{$campaign->id}-opened", TagType::MAILCOACH);
    }
}
