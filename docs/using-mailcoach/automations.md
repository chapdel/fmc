---
title: Automations
weight: 3
---

Mailcoach allows you to set up automations that use different "actions" to determine the flow and mails sent to your subscribers.

![automations list](/docs/laravel-mailcoach/v4/images/automations/index.png)

The Campaigns index page summarizes some information about your automations. You can see the status of the automation (running or paused), and when it was last updated.

## Are you a visual learner?

This video shows you a general introduction to automations.

<iframe width="560" height="315" src="https://www.youtube.com/embed/pZgwdF2tOxU" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

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

The screenshot shows an example of a welcome email, that waits 1 hour before sending the "Welcome" automation mail. More about setting up [automation mails here](/docs/laravel-mailcoach/v4/using-mailcoach#automation-mails).

It is also possible to [create custom actions](/docs/laravel-mailcoach/v4/automations/creating-custom-actions).

## Run

![run automation](/docs/laravel-mailcoach/v4/images/automations/run.png)

This screen allows you to configure the interval at which subscribers will move through the actions. This setting allows you to limit the performance impact of large lists if, for example, you're waiting for several days between each step.

The Start/Pause button is pretty self-explanatory, it allows you start & pause the automation.

## Automation mails

When creating an automation mail, you can either create a new one from scratch or duplicate an existing one to copy most of its settings:

![screenshot](/docs/laravel-mailcoach/v4/images/automations/creating-an-automation-mail-index.png)

### Settings

![screenshot](/docs/laravel-mailcoach/v4/images/automations/creating-an-automation-mail-settings.png)

Most of the settings for creating a new automation mail are pretty self-explanatory:

The name of an automation mail is only used within your Mailcoach UI. Subscribers will not see this anywhere.

The value for the _Subject_ field is used as the subject for sent emails.

Finally, the tracking options allow you to track how many subscribers have opened your email and whether they clicked any of the links you included. You can see what the result of this tracking looks like in the [Automation mail statistics](/docs/laravel-mailcoach/v4/using-the-ui/automations#automation-mail-statistics) section of the documentation.

### Content

![screenshot](/docs/laravel-mailcoach/v4/images/automations/creating-an-automation-mail-content.png)

This is the content of the email that will be sent to your subscribers. If you duplicated another campaign, this field will be the same as that automation mail's content.

You can -_and should_- use placeholders in your emails. We strongly suggest including at least the `::unsubscribeUrl::` in every email you send, for example at the bottom of the email. Not including this link may lead to your users complaining or emails being marked as spam.

While editing your email, you can see what it will look like for subscribers by clicking the _Preview_ button.

Since we are sending out mails in UTF-8, it's good practice including following charset definition in the `<head>` of your HTML as well. This way, special characters will also correctly show up in your preview.

```HTML
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
```

### Delivery

![screenshot](/docs/laravel-mailcoach/v4/images/automations/creating-an-automation-mail-delivery.png)

This page provides a final checklist that you should go over before sending an automation mail. It shows a summary of the mail's settings, the subject and any issues we found with your email's content.

You can also send a test email to yourself for a final review before sending it out to your subscribers' mailboxes.

## Mail statistics

### Summary

This page contains all the statistics for an automation mail that is being sent. These statistics will be updated regularly, so you can closely track the success of your mails.

![screenshot](/docs/laravel-mailcoach/v4/images/automations/automation-mail-statistics-overview.png)

This page shows a summary of the statistics for your campaign. At a glance, you can see how many people opened your email and clicked any links you included, and also how many people unsubscribed from your mailing list as a result of this campaign and how often the emails bounced.

A bounce means that an email could not be delivered to a certain email address, this could happen for several reasons. You can better find out why in the [_Outbox_](/docs/laravel-mailcoach/v4/using-the-ui/automations#outbox) tab.

### Opens and clicks

![screenshot](/docs/laravel-mailcoach/v4/images/automations/automation-mail-statistics-opens.png)

On the _Opens_ screen, you can see which subscribers have already opened your email, and when.

![screenshot](/docs/laravel-mailcoach/v4/images/automations/automation-mail-statistics-clicks.png)

The _Clicks_ screen has information on which links were opened and how many times. The _Unique clicks_ column concerns how many unique users have clicked your link.

### Unsubscribes

![screenshot](/docs/laravel-mailcoach/v4/images/automations/automation-mail-statistics-unsubscribes.png)

You can also see how many people, regrettably, clicked the unsubscribe link in this campaign.

### Outbox

![screenshot](/docs/laravel-mailcoach/v4/images/automations/automation-mail-statistics-outbox.png)

Here, you can see a collection of all the individual emails that were sent to your subscribers, and whether they encountered any issues upon arriving at their destination.

If you're sending this campaign to a large mailing list, some emails may not have been sent yet, but don't worry, it should clear up within a couple of minutes.
