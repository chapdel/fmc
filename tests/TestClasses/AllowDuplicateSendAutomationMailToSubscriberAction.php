<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Automation\Actions\SendAutomationMailToSubscriberAction;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class AllowDuplicateSendAutomationMailToSubscriberAction extends SendAutomationMailToSubscriberAction
{
    public function execute(AutomationMail $automationMail, ActionSubscriber $actionSubscriber): void
    {
        $this
            ->prepareEmailHtml($automationMail)
            ->prepareWebviewHtml($automationMail)
            ->createSend($automationMail, $actionSubscriber);
    }

    protected function createSend(AutomationMail $automationMail, ActionSubscriber $actionSubscriber): Send
    {
        return $automationMail->contentItem->sends()->create([
            'subscriber_id' => $actionSubscriber->subscriber_id,
            'uuid' => (string) Str::uuid(),
        ]);
    }
}
