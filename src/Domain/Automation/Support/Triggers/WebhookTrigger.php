<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Triggers;

class WebhookTrigger extends AutomationTrigger
{
    public static function getName(): string
    {
        return (string) __('mailcoach - Call a webhook to trigger the automation');
    }

    public static function getComponent(): ?string
    {
        return 'mailcoach::webhook-trigger';
    }
}
