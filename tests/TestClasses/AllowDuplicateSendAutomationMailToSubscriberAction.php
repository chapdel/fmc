<?php


namespace Spatie\Mailcoach\Tests\TestClasses;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Actions\SendAutomationMailToSubscriberAction;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class AllowDuplicateSendAutomationMailToSubscriberAction extends SendAutomationMailToSubscriberAction
{
    public function execute(AutomationMail $automationMail, Subscriber $subscriber): void
    {
        $this
            ->prepareSubject($automationMail)
            ->prepareEmailHtml($automationMail)
            ->prepareWebviewHtml($automationMail)
            ->createSend($automationMail, $subscriber);
    }

    protected function createSend(AutomationMail $automationMail, Subscriber $subscriber): Send
    {
        return $automationMail->sends()->create([
            'subscriber_id' => $subscriber->id,
            'uuid' => (string)Str::uuid(),
        ]);
    }
}
