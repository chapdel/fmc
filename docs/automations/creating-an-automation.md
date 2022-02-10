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

Mailcoach also allows you to create [custom triggers](/docs/laravel-mailcoach/v5/automations/creating-custom-triggers).

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

Mailcoach also allows you to create [custom actions](/docs/laravel-mailcoach/v5/automations/creating-custom-actions) and [custom conditions](/docs/laravel-mailcoach/v5/automations/creating-conditions).

## About Halt actions

The Halt action removes the subscriber from the automation when it reaches it, you usually put this at the end of your automation (or sometimes in an if/else block). Mailcoach works the following way:

1. Loop over each automation
2. Loop over the actions in that automation
3. Loop over the subscribers that are attached to the action and run the action for it

If you don't halt the automation for a subscriber that is at the end, it will keep doing those steps for that subscriber indefinitely.

This can be a useful feature for when you have a drip campaign that you want to attach more emails to in the future, but want to already start the campaign for subscribers, then the subscribers will move to the next action once that is added.

For simple welcome automations, or automations that are completely set up, it's recommended to add a Halt action at the end.
