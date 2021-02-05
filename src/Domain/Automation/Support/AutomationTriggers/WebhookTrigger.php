<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\AutomationTriggers;

class WebhookTrigger extends AutomationTrigger
{
    public static function getName(): string
    {
        return __('Call a webhook to trigger the automation');
    }

    public static function getComponent(): ?string
    {
        return 'webhook-trigger';
    }
}
