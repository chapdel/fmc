---
title: Troubleshooting errors during sending
weight: 12
---

When sending an automation mail, the package will create `Send` models for each mail to be sent. A `Send` has a property `sent_at` that stores the date time of when an email was actually sent. If that attribute is `null` the email has not yet been sent.

If you experience problems while sending, and the state of your queues has been lost, you should dispatch a `SendAutomationMailJob` for each `Send` that has `sent_at` set to `null` and the `automation_mail_id` is not `null`.

```php
Send::query()
    ->whereNull('sent_at')
    ->whereNotNull('automation_mail_id')
    ->each(function(Send $send) {
       dispatch(new SendAutomationMailJob($automationMailSend);
    });
```

You can run the above code by executing this command:

```bash
php artisan mailcoach:retry-pending-automation-mail-sends
```

Should, for any reason, two jobs for the same `Send` be scheduled, it is highly likely that only one mail will be sent. After a `SendAutomationMailJob` has sent an email it will update `sent_at` with the current timestamp. The job will not send a mail for a `Send` whose `sent_at` is not set to `null`.
