---
title: Viewing automation mail statistics
weight: 7
---

After an automation mail is sent, some statistics will be made available.

## Available statistics

### On an automation mail

The [scheduled](/docs/laravel-mailcoach/v4/installation/in-an-existing-laravel-app#schedule-the-commands) 'email-campaigns:calculate-statistics' will fill these attributes on the `AutomationMail` model:

- `sent_to_number_of_subscribers`
- `open_count`: this is the total number of times your automation mail was opened. Multiple opens by a single subscriber will be counted.
- `unique_open_count`: the number of subscribers that opened your automation mail.
- `open_rate`: the `unique_open_count` divided by the `sent_to_number_of_subscribers`. The result is multiplied by 100. The maximum value for this attribute is 100, and the minimum is 0.
- `click_count`: the total number of times the links in your automation mail were clicked. Multiple clicks on the same link by a subscriber will be counted.
- `unique_click_count`: the number of subscribers who clicked any of the links in your automation mail.
- `click_rate`: the `unique_click_count` divided by the `sent_to_number_of_subscribers`. The result is multiplied by 100. The maximum value for this attribute is 100, the minimum is 0.
- `unsubscribe_count`: the number of people that unsubscribed from the email list using the unsubscribe link from this automation mail
- `unsubscribe_rate`: the `unsubscribe_count` divided by the `sent_to_number_of_subscribers`. The result is multiplied by 100. The maximum value for this attribute is 100, the minimum is 0.

You can also get the opens and clicks stats for an automation mail. Here's an example using the `opens` relation to retrieve who first opened the mail.

```php
$open = $automationMail->opens->first();
$email = $open->subscriber->email;
```

## On an automation mail link

If you enabled click tracking, an `AutomationMailLink` will have been created for each link in your automation mail.

It contains these two attributes that hold statistical data:

- `click_count`: the total number of times this link was clicked. Multiple clicks on the same link by a subscriber will each be counted.
- `unique_click_count`: the number of subscribers who clicked this link.

To know who clicked which link, you can use the relations on `AutomationMailLink` model. Here's an example where we get the email of the subscriber who first clicked the first link of a mail.

```php
$automationMailLink = $automationMail->links->first();
$automationMailClick = $automationMailLink->links->first();
$email = $automationMailClick->subscriber->email;
```

## When are statistics calculated

The statistics are calculated by the scheduled `mailcoach:calculate-automation-mail-statistics`. This job will recalculate statistics:

- each minute for campaigns that were sent between 0 and 5 minutes ago
- every 10 minutes for campaigns that were send between 5 minutes and two hours ago
- every hour for campaigns that were sent between two hours and a day
- every four hours for campaigns that were sent between a day and two weeks ago

After two weeks, no further statistics are calculated.

## Manually recalculate statistics

To manually trigger a recalculation, execute the command using the campaign id as a parameter.

```bash
php artisan mailcoach:calculate-automation-mail-statistics <automation-mail-id>
```
