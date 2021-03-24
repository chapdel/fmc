---
title: Creating an automation
weight: 2
---

An automation can be created like this:

```php
Automation::create()
    ->name('Welcome email')
    ->to($emailList)
    ->runEvery(CarbonInterval::minute())
    ->triggerOn(new SubscribedTrigger)
    ->chain([
        new SendAutomationMailAction($automationMail),
    ])
    ->start();
```

The `runEvery` call is optional, accepts a `CarbonInterval` and will run every minute by default.

## Setting the trigger

The trigger is what starts your automation, in most cases the `\Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger` will be used, this one triggers the automation once a subscriber is subscribed and confirmed.

Mailcoach ships with multiple triggers:

- DateTrigger: Triggers on a date & time
- NoTrigger: No trigger, which allows you to trigger the automation from code by calling `$automation->run($subscriber)` on a subscriber.
- SubscribedTrigger: Triggers when a user is subscribed & confirmed
- TagAddedTrigger: When a tag gets added to a subscriber
- TagRemovedTrigger: When a tag gets removed from a subscriber
- WebhookTrigger: Trigger the automation by calling a webhook

Mailcoach also allows you to create [custom triggers](/docs/laravel-mailcoach/v4/automations/creating-custom-triggers).

## Passing actions

The `chain` method accepts an array of automation actions that a subscriber will pass through once triggered.

Mailcoach ships with multiple actions:

- SendAutomationMailAction: Send an automation mail
- AddTagsAction: Add one or more tags to a subscriber
- RemoveTagsAction: Remove one or more tags from a subscriber
- ConditionAction: Branch the automation in `true` & `false` branches based on a condition, Mailcoach ships with the following conditions:
  - HasTagCondition: Whether the subscriber has a certain tag
  - HasClickedAutomationMail: Whether the subscriber has clicked one or any link in an automation mail
  - HasOpenedAutomationMail: Whether the subscriber has opened an automation mail
- HaltAction: Stop the automation
- UnsubscribeAction: Unsubscribe the subscriber from the list
- WaitAction: Wait for a set interval before continuing to the next action

Mailcoach also allows you to create [custom actions](/docs/laravel-mailcoach/v4/automations/creating-custom-actions) and [custom conditions](/docs/laravel-mailcoach/v4/automations/creating-conditions).

