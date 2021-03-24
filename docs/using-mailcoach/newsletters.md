---
title: Newsletters
weight: 2
---

An email campaign is a set of emails that can be sent to an email list (or a segment of a list).

![screenshot](/docs/laravel-mailcoach/v4/images/campaigns/index.png)

The _Campaigns_ index page summarizes some information about your campaigns. You can see the status of the campaign (editing, scheduled, sending or sent), the number of people it was sent to, the number of times it was opened and how many links were clicked, and when it was or will be sent. 

The percentage below the opens and clicks is as part of the total amount of subscribers that received your email.

## Are you a visual learner?

This video shows you a general introduction to using campaigns.

<iframe width="560" height="315" src="https://www.youtube.com/embed/YJ7O46P6X9A" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

## Creating a campaign

When creating a campaign, you can either create a new one from scratch or duplicate an existing one to copy most of its settings:

![screenshot](/docs/laravel-mailcoach/v4/images/campaigns/creating-a-campaign-index.png)

When creating a new campaign, you can choose which of your templates, if any, you want to use as a base for your email's content. You can also choose to duplicate an existing campaign. Use these actions to save time when creating a new campaign.

### Settings

![screenshot](/docs/laravel-mailcoach/v4/images/campaigns/creating-a-campaign-settings.png)

Most of the settings for creating a new campaign are pretty self-explanatory:

The name of a campaign is only used within your Mailcoach UI. Subscribers will not see this anywhere.

The value for the _Subject_ field is used as the subject for sent emails.

The _List_ menu allows you to pick one of your lists, which you can further narrow down by picking one of its _Segments_ right below it. You can read more about tags and segments [here](docs/laravel-mailcoach/v4/using-the-ui/lists#tags-and-segments).

Finally, the tracking options allow you to track how many subscribers have opened your email and whether they clicked any of the links you included. You can see what the result of this tracking looks like in the [Campaign statistics](/docs/laravel-mailcoach/v4/using-the-ui/campaigns#campaign-statistics) section of the documentation.

### Content

![screenshot](/docs/laravel-mailcoach/v4/images/campaigns/creating-a-campaign-content.png)

This is the content of the email that will be sent to your subscribers. If you selected a template while creating your campaign, the field will be prefilled with that template. If you duplicated another campaign, this field will be the same as that campaign's content.

You can -_and should_- use placeholders in your emails. We strongly suggest including at least the `::unsubscribeUrl::` in every email you send, for example at the bottom of the email. Not including this link may lead to your users complaining or emails being marked as spam.

While editing your email, you can see what it will look like for subscribers by clicking the _Preview_ button.

Since we are sending out mails in UTF-8, it's good practice including following charset definition in the `<head>` of your HTML as well. This way, special characters will also correctly show up in your preview.

```HTML
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
```

### Delivery

![screenshot](/docs/laravel-mailcoach/v4/images/campaigns/creating-a-campaign-delivery.png)

This page provides a final checklist that you should go over before sending a campaign. It shows a summary of the campaign's settings, like how many people will be sent an email, the subject and any issues we found with your email's content.

You can also send a test email to yourself for a final review before sending it out to your subscribers' mailboxes.

Finally, you can set the timing for this campaign: whether to send it right now or to schedule delivery for a moment in the future. You can use this to set up multiple campaigns to be sent without you having to micromanage the delivery. As long as the campaign hasn't started sending emails, you can reschedule or cancel it.

When sending a campaign, all the emails that need to be sent out will be placed in a queue, and you will be redirected to a page where you can track the progress and see your campaign's statistics trickling in:

![screenshot](/docs/laravel-mailcoach/v4/images/campaigns/campaign-statistics-sending.png)

For more information on what these statistics mean, continue reading in the [campaign's statistics](/docs/laravel-mailcoach/v4/campaigns/campaign-statistics) section of the documentation.

## Campaign statistics

This page contains all the statistics for a campaign that was sent. These statistics will be updated regularly, so you can closely track the success of your campaigns.

### Summary

![screenshot](/docs/laravel-mailcoach/v4/images/campaigns/campaign-statistics-overview.png)

This page shows a summary of the statistics for your campaign. At a glance, you can see how many people opened your email and clicked any links you included, and also how many people unsubscribed from your mailing list as a result of this campaign and how often the emails bounced.

A bounce means that an email could not be delivered to a certain email address, this could happen for several reasons. You can better find out why in the [_Outbox_](/docs/laravel-mailcoach/v4/using-the-ui/campaigns#outbox) tab.

### Opens and clicks

![screenshot](/docs/laravel-mailcoach/v4/images/campaigns/campaign-statistics-opens.png)

On the _Opens_ screen, you can see which subscribers have already opened your email, and when.

![screenshot](/docs/laravel-mailcoach/v4/images/campaigns/campaign-statistics-clicks.png)

The _Clicks_ screen has information on which links were opened and how many times. The _Unique clicks_ column concerns how many unique users have clicked your link.

## Unsubscribes

![screenshot](/docs/laravel-mailcoach/v4/images/campaigns/campaign-statistics-unsubscribes.png)

You can also see how many people, regrettably, clicked the unsubscribe link in this campaign.

### Outbox

![screenshot](/docs/laravel-mailcoach/v4/images/campaigns/campaign-statistics-outbox.png)

Here, you can see a collection of all the individual emails that were sent to your subscribers, and whether they encountered any issues upon arriving at their destination.

If you're sending this campaign to a large mailing list, some emails may not have been sent yet, but don't worry, it should clear up within a couple of minutes.

## Using templates
Templates provide a starting point for a campaign's email content.

![screenshot](/docs/laravel-mailcoach/v4/images/templates/create.png)

You can preview what this template will look like in a user's email client by creating a campaign based on this template, and visiting the campaign's _Content_ tab.

You can use placeholders for certain Mailcoach actions in your links:

- `::webViewUrl::` This URL will display the HTML of the campaign
- `::subscriber.first_name::` The first name of the subscriber
- `::subscriber.email::` The email of the subscriber
- `::list.name::` The name of email list this campaign is sent to
- `::unsubscribeUrl::` The URL where users can unsubscribe
