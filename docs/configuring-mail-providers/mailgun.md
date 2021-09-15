---
title: Mailgun
weight: 4
---

Mailcoach supports sending mails via Mailgun. This page contains instructions to [set up Mailgun in the standalone Mailcoach app](/docs/laravel-mailcoach/v4/configuring-mail-providers/mailgun#using-mailgun-in-the-mailcoach-standalone-app) and [in a Laravel app with `laravel-mailcoach`](/docs/laravel-mailcoach/v4/configuring-mail-providers/mailgun#handling-mailgun-feedback-in-an-existing-laravel-app).

## Using Mailgun in the Mailcoach standalone app

You should only follow these instructions when you've installed Mailcoach in an existing app.

First off, make sure you have created an account with Mailgun and that you have verified your domain with them. Check [Mailgun's documentation](https://documentation.mailgun.com/en/latest/user_manual.html#verifying-your-domain) on how to do this.

Go to your _Domain settings_ in Mailgun and make sure the _Click tracking_ and _Open tracking_ options are enabled:

![screenshot](/docs/laravel-mailcoach/v4/images/mailgun-domain-settings.png)

Mailgun needs to know where to send your campaigns' statistics. Go to your Mailcoach _Mail config_ page (in the user menu on the top right), select _Mailgun_ as your driver, and copy the webhook that is mentioned in your Mail configuration:

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/mailgun-copy-webhook.png)

Then, go to the _Webhooks_ submenu in Mailgun, and, with the webhook URL that you copied in the last step, add new webhooks for the following event types: _Clicks_, _Opens_, _Permanent Failure_, and _Spam Complaints_:

![screenshot](/docs/laravel-mailcoach/v4/images/mailgun-webhooks.png)

For Mailcoach to know the statistics are trustworthy, it needs to know your Mailgun HTTP webhook signing key, which you can find on the same page:

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/mailgun-copy-webhook-signing-key.png)

Copy it, and paste it in the _Webhook signing secret_ field in your mail configuration:

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/mailgun-copy-webhook-signing-key.png)

### Configuring Mailcoach

Mailcoach still needs to know where to send its emails, so go to _Overview_ page in your Mailgun dashboard, select the _API_ option, then _cURL_ and take note of the _API key_ and _API base URL_ that are now visible:

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/mailgun-api-key.png)

Go to your _Mail configuration_ page in Mailcoach, and fill in the fields:

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/mailgun-setup-mail-config.png)

- Mails per second: this will be different according to your Mailgun plan, you can find an appropriate value for this in your Mailgun dashboard
- Domain: the same domain you did the setup for in Mailgun
- Secret: the _API key_ you copied in the last step
- Endpoint: the _API base URL_ that you copied in the last step, but without the `https://` and everything after the top-level domain (`.net` in my case)

## Handling Mailgun feedback in an existing Laravel app

The `spatie/laravel-mailcoach-mailgun-feedback` package can handle bounce feedback coming from Mailgun. All e-mail address that permanently bounce will be unsubscribed from all lists.

You can install the add-on package via Composer:

```bash
composer require spatie/laravel-mailcoach-mailgun-feedback:^3.0
```

### Adding the webhooks route

You must use this route macro in your route service provider. Do **NOT** apply the `web` group middleware to this route as that would cause an unnecessary session to be started for each webhook call.

You can replace `mailgun-feedback` with any url you'd like.


```php
Route::mailgunFeedback('mailgun-feedback');
```

### Configuring webhooks

At Mailgun you must [configure a new webhook](https://www.mailgun.com/blog/a-guide-to-using-mailguns-webhooks/).

At the webhooks settings screen, on mailgun, you must add the `clicked`, `complained`, `opened` and `permanent_fail` webhooks and point them to the route you configured. In the screenshot below we configured the webhooks using a `ngrok.io` domain.

![Mailgun webhooks](/docs/laravel-mailcoach/v4/images/mailgun-webhooks.png)

At the domain settings you must enable click and open tracking

![Mailgun webhooks](/docs/laravel-mailcoach/v4/images/mailgun-domain-settings.png)


In the `mailcoach` config file you must add this section.

```php
// in config/mailcoach.php

    'mailgun_feedback' => [
        'signing_secret' => env('MAILGUN_SIGNING_SECRET'),
   ],
```

In your `.env` you must add a key `MAILGUN_SIGNING_SECRET` with the Mailgun signing secret you'll find at the Mailgun dashboard as its value.

### Using the correct mail driver

If you haven't done so already, you must configure Laravel to use the Mailgun driver. Follow the instruction in [the mail section of the Laravel docs](https://laravel.com/docs/7.x/mail#driver-prerequisites).

### Informing Mailgun

Before start sending campaigns via Mailgun we highly recommend getting in touch with their support and let them know the amount of mails your email list contains. Usually they will adjust the sending limits of your account so it's not a problem to send a large volumes in a short amount of time.
