---
title: Running automations more than once
weight: 13
---

In certain cases you might want to allow subscribers to run through an automation more than once.

This can be done by extending/overwriting the necessary actions in the Mailcoach config file.

## ShouldAutomationRunForSubscriberAction

The first one is the `ShouldAutomationRunForSubscriberAction` class. You can overwrite this action by changing the `mailcoach.automation.actions.should_run_for_subscriber` config.

An action that allows duplicate runs could look like this:

```php
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class AllowDuplicateShouldAutomationRunForSubscriberAction
{
    use UsesMailcoachModels;

    public function execute(Automation $automation, Subscriber $subscriber): bool
    {
        if (! $subscriber->isSubscribed()) {
            return false;
        }

        if (! $automation
            ->newSubscribersQuery()
            ->where("{$this->getSubscriberTableName()}.id", $subscriber->id)
            ->exists()
        ) {
            return false;
        }

        return true;
    }
}
```

If you compare it to the original, you can see that just the first check (where we check if the subscriber is already in the automation) has been left out.

## Sending automation mails more than once

A second class you might want to overwrite is the `SendAutomationMailToSubscriberAction`, which by default has a check that makes sure the mail hasn't been sent to the Subscriber before. You can change this class in the `mailcoach.automation.actions.send_automation_mail_to_subscriber` config key.

An implementation that would allow duplicate sends could look like this:

```php
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Actions\SendAutomationMailToSubscriberAction;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class AllowDuplicateSendAutomationMailToSubscriberAction extends SendAutomationMailToSubscriberAction
{
    public function execute(AutomationMail $automationMail, Subscriber $subscriber): void
    {
        // The check from the default action here has been left out.
        
        $this
            ->prepareSubject($automationMail)
            ->prepareEmailHtml($automationMail)
            ->prepareWebviewHtml($automationMail)
            ->sendMail($automationMail, $subscriber);
    }

    protected function createSend(AutomationMail $automationMail, Subscriber $subscriber): Send
    {
        // The check from the default action here has been left out.
    
        return $automationMail->sends()->create([
            'subscriber_id' => $subscriber->id,
            'uuid' => (string)Str::uuid(),
        ]);
    }
}
```

**Important to note here is that if the `SendAutomationMailJob` fails, restarting it will create another send, which could result in more emails sent than intended.**
