---
title: Automations
weight: 3
---

Mailcoach allows you to set up automations that use different "actions" to determine the flow and mails sent to your subscribers.

![automations list](/docs/laravel-mailcoach/v4/images/automations/index.png)

The Campaigns index page summarizes some information about your automations. You can see the status of the automation (running or paused), and when it was last updated.

## Creating an automation

When creating an automation, you can either create a new one from scratch or duplicate an existing one to copy most of its settings:

![creating an automation](/docs/laravel-mailcoach/v4/images/automations/create.png)

## Settings

![automation settings](/docs/laravel-mailcoach/v4/images/automations/settings.png)

Most of the settings for creating a new automation are pretty self-explanatory:

The name of an automation is only used within your Mailcoach UI. Subscribers will not see this anywhere.

The trigger determines how subscribers enter your automation, the most commonly used one is the "When a user subscribes" option. Some triggers might have some extra fields, like the "On a date" tirgger that lets you set up the date & time. It is also possible to [create custom triggers](/docs/laravel-mailcoach/v4/automations/creating-custom-triggers)

The List menu allows you to pick one of your lists, which you can further narrow down by picking one of its Segments right below it. You can read more about [tags and segments here](/docs/laravel-mailcoach/v4/using-mailcoach/audience#tags-and-segments).

## Actions

![automation actions](/docs/laravel-mailcoach/v4/images/automations/actions.png)

This is where you'll define the flow of the automation. You can add actions by clicking on the "+" and choosing an action to add to your automation flow.

![automation actions](/docs/laravel-mailcoach/v4/images/automations/add-action.png)

The screenshot shows an example of a welcome email, that waits 1 hour before sending the "Welcome" automation mail. More about setting up [automation mails here](/docs/laravel-mailcoach/v4/using-mailcoach/automation-mails).

It is also possible to [create custom actions](/docs/laravel-mailcoach/v4/automations/creating-custom-actions).

## Run

![run automation](/docs/laravel-mailcoach/v4/images/automations/run.png)

This screen allows you to configure the interval at which subscribers will move through the actions. This setting allows you to limit the performance impact of large lists if, for example, you're waiting for several days between each step.

The Start/Pause button is pretty self-explanatory, it allows you start & pause the automation.


