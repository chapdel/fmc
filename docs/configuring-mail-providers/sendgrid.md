---
title: Sendgrid
weight: 6
---

Mailcoach supports sending mails via Sendgrid. This page contains instructions to [set up Sendgrid in the standalone Mailcoach app](/docs/laravel-mailcoach/v4/configuring-mail-providers/sendgrid#using-sendgrid-in-the-mailcoach-standalone-app) and [in a Laravel app with `laravel-mailcoach`](/docs/laravel-mailcoach/v4/configuring-mail-providers/sendgrid#handling-sendgrid-feedback-in-an-existing-laravel-app).

## Using Sendgrid in the Mailcoach standalone app

To get started, make sure your SendGrid account is completely set up and your domain has been verified. The verification steps are outside of the scope of this tutorial, so we refer you to [SendGrid's docs](https://sendgrid.com/docs/ui/account-and-settings/how-to-set-up-domain-authentication/) to get these steps done. Sending out emails without a verified domain is possible, but not recommended, as the chances of your emails being flagged as spam are very high.

The value for the _mails per second_ field depends on your SendGrid account and settings. A good starting point is 5 mails per second.

### Email configuration

Setting up your email configuration with SendGrid is very easy. Open your SendGrid dashboard on the [API keys page](https://app.sendgrid.com/settings/api_keys) and press the _Create API Key_ button in the top right corner:

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/sendgrid-api-key-create.png)

Choose a name for your API key, and select the _Resticted Access_ permission. Mailcoach only needs the _Mail Send_ and _Tracking_ permissions:

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/sendgrid-api-key-permissions.png)

Create the API key, and click the text field on the next screen to copy the newly created key. Make sure to save this in a safe spot, like a secure password manager.

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/sendgrid-api-key-copy.png)

Go to your Mailcoach Mail configuration page (under your user menu), make sure you have selected _SendGrid_ as your driver, and paste the API Key we just created there:

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/sendgrid-api-key-in-mailcoach.png)

### Tracking events

If you are not there already, go to your Mailcoach Mail configuration page (under your user menu), make sure your driver is set to _SendGrid_, and choose a value for your _Webhook signing secret_. This should be an unguessable string, but don't use any passwords that you're already using. This secret will be used by Mailcoach to verify incoming events from SendGrid.

Also, copy the webhook URL that is mentioned on the same page and save your configuration.

Go to your SendGrid dashboard, open the _Settings_ menu and go to your [_Mail Settings_](https://app.sendgrid.com/settings/mail_settings). Open and enable the _Event Notification_ setting. In the _HTTP POST URL_ field, paste the webhook URL that you just copied, and replace _YOUR-WEBHOOK-SIGNING-SECRET_ by the webhook signing secret that you chose in the last step.

At the bottom, enable these actions to be reported back to Mailcoach: _Dropped_, _Bounced_, _Opened_, and _Clicked_. Finally, press the blue checkmark in the top right corner to save your configuration:

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/sendgrid-event-notifications.png)

Your SendGrid configuration should now be complete, and you can go ahead and try sending a test mail. It may go to your spam if you have not set up your domain settings.

## Handling Sendgrid feedback in an existing Laravel app

You should only follow these instructions when you've installed Mailcoach in an existing app.

The `spatie/laravel-mailcoach-sendgrid-feedback` package can handle bounce feedback coming from Sendgrid. All e-mail address that permanently bounce will be unsubscribed from all lists.

You can install the add-on package via composer:

```bash
composer require spatie/laravel-mailcoach-sendgrid-feedback:^4.0
```

### Adding the webhooks route

You must use this route macro in your route service provider. Do **NOT** apply the `web` group middleware to this route as that would cause an unnecessary session to be started for each webhook call.

You can replace `sendgrid-feedback` with any url you'd like.


```php
Route::sendgridFeedback('sendgrid-feedback');
```

### Configuring webhooks

At Sendgrid you must [configure a new webhook](https://sendgrid.com/docs/for-developers/tracking-events/getting-started-event-webhook/).

At the webhooks settings screen at sendgrid you must add the `Bounced`, `Opened`, `Clicked` and `Mark as Spam` webhooks and point them to the route your configured. In the screenshot below we configured the webhooks using a `ngrok.io` domain with a `?secret=yolo-no-real-signature` appended to the webhook url.

![Sendgrid webhooks](/docs/laravel-mailcoach/v4/images/sendgrid-webhooks.png)

At the Tracking settings you must enable click and open tracking

![Mailgun webhooks](/docs/laravel-mailcoach/v4/images/sendgrid-tracking-settings.png)

In the `mailcoach` config file you must add this section.

```php
// in config/mailcoach.php

    'sendgrid_feedback' => [
        'signing_secret' => env('SENDGRID_SIGNING_SECRET'),
   ],
```

In your `.env` you must add a key `SENDGRID_SIGNING_SECRET` with the Sendgrid signing secret you defined when setting up the webhook.

### Using the correct mail driver

If you haven't done so already, you must configure Laravel to use the Sendgrid driver. Follow the instruction in [the mail section of the Laravel docs](https://laravel.com/docs/7.x/mail#driver-prerequisites).

### Informing Sendgrid

Before start sending campaigns via Sendgrid we highly recommend getting in touch with their support and let them now the amount of mails your email list contains. Usually they will adjust the sending limits of your account so it's not a problem to send a large volumes in a short amount of time.
