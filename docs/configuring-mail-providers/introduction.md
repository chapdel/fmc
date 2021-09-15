---
title: Introduction
weight: 1
---

We recommend using one of these providers to send out mail.

- [Amazon SES](/docs/laravel-mailcoach/v4/configuring-mail-providers/amazon-ses)
- [SendGrid](/docs/laravel-mailcoach/v4/configuring-mail-providers/sendgrid)
- [Mailgun](/docs/laravel-mailcoach/v4/configuring-mail-providers/mailgun)
- [Postmark](/docs/laravel-mailcoach/v4/configuring-mail-providers/postmark)

Mailcoach also supports sending via SMTP. When using SMTP, open and click tracking will not be available.

## Sending test mails using the UI

When you've installed Mailcoach as a standalone-app, you can visit the mail configuration screen to send a test email. You can send a test email to verify if the mail configuration is ok. If this test mail ends up in your spam, there may be something wrong with your domain or DNS settings.

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/successful-test-mail.png)

### Sending confirmation and welcome mails with a different account

Some email providers have very strict rules on sending mails. They require to keep a low bounce rate at all times. Confirmation mails have a higher chance of bouncing because they are sent to unverified email addresses.

To keep your primary email service happy, you can opt to use a different account to send out confirmation and welcome mails.

If you've installed Mailcoach as a standalone app, configure a transactional mailer in the "Transaction mail" section of the settings.

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/transactional.png)

If you've installed `laravel-mailcoach` in an existing application, you can set the `transactional.mailer` key in the `mailcoach` config file to the name of the mailer you'd like to use for transactional mails.
