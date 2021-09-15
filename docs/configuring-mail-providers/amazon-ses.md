---
title: Amazon SES
weight: 3
---

Mailcoach supports sending mails via Amazon SES. This page contains instructions to [set up Amazon SES in the standalone Mailcoach app](/docs/laravel-mailcoach/v4/configuring-mail-providers/amazon-ses#using-amazon-ses-in-the-mailcoach-standalone-app) and [in a Laravel app with `laravel-mailcoach`](/docs/laravel-mailcoach/v4/configuring-mail-providers/amazon-ses#handling-amazon-ses-feedback-in-an-existing-laravel-app).

## Using Amazon SES in the Mailcoach standalone app

To send mails with Amazon SES, you need to do some configuration in the AWS dashboard first. To get started, open your AWS dashboard, select your preferred region and go to Simple Email Service under _Services_.

Firstly, you need to verify your domain and the email address mails will come from (your _From email_ in Mailcoach lists). Both of these are out of the scope of this tutorial, so we refer you to [SES' documentation](https://docs.aws.amazon.com/ses/latest/DeveloperGuide/setting-up-email.html). You can send campaigns from SES without verifying your domain, however, this might cause your mails to be flagged as spam by mail clients, and you will not be able to track any statistics from your campaigns.

The value for the _mails per second_ field depends on your AWS account and settings. A good starting point is 5 mails per second.

### Key and Secret (sending emails)

Your SES key and secret, or _Access Key ID_ and _Secret Access Key_ as they are called in AWS, are the credentials Mailcoach needs to be able to send emails using SES. To know more about these keys, read about them in the [AWS documentation](https://docs.aws.amazon.com/general/latest/gr/aws-sec-cred-types.html#access-keys-and-secret-access-keys).

To get your key and secret, you need to create a new user. Go to the _IAM_ service in AWS, and then to _Users_ under _Access management_ and press the _Add user_ button:

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/amazon-ses-iam.png)

Choose a name for your new user, and make sure to enable _Programmatic access_. This allows it to send requests to the AWS API, and is required to be able to send mails from a third party platform (like Mailcoach):

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/amazon-ses-programmatic-access.png)

Go to the next page, and set the permissions for the user. Since we won't be creating multiple users in this tutorial, we will simply use the _Attach existing policies directly_ option to add the _AmazonSESFullAccess_ permission to this user:

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/amazon-ses-permissions.png)

Go over to the next page. We are skipping tags for now and continuing to the _Review_ page. Make sure the details are correct and verify creating the user.

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/amazon-ses-review.png)

If everything went OK, you should now be able to see your user's Access Key ID and Secret access key:

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/amazon-ses-key-and-secret.png)

Go to the Mailcoach Mail Configuration page (in the user menu in the top right), make sure you have selected the _Amazon SES_ driver and enter the Key and Secret:

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/amazon-ses-key-and-secret-in-mailcoach.png)

### Configuration Set (tracking events)

Amazon SES requires users to track any bounced messages, you need to create an SES Configuration Set so Mailcoach can track these.

Open your AWS Dashboard and make sure you still have the same AWS region selected as where you verified your domain and sending email address. Go to the _Simple Email Service_ under _Services_, and find the _Configuration Sets_ menu item under _Email Sending_. Press the _Create Configuration Set_ button:

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/amazon-ses-configuration-set.png)

Choose a name for your configuration set and create it, then click the newly created item in the list to add some events destinations. Click the _Select a destination type_ dropdown and select the _SNS_ option:

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/amazon-ses-destination-type.png)

In the window that pops up, choose a name and select the following event types: _Reject_, _Bounce_,  _Complaint_, _Click_ and _Open_. Next, press the _Topic_ dropdown and choose _Create SNS Topic_:

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/amazon-ses-sns-destination.png)

Another window will open, choose a topic and display name, press _Create Topic_ and _Save_. You should now see your newly created Configuration Set:

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/amazon-ses-finished-config-set.png)

Now, go to Simple Notification Service (SNS) in your AWS dashboard to further configure the topic you just created. Open the _Topics_ menu and select the topic.

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/amazon-ses-edit-sns-topic.png)

Press the _Create subscription_ button on this page, choose the _HTTPS_ protocol and enter the webhook URL that you can find on your Mailcoach Mail configuration page (`https://YOUR-DOMAIN.com/ses-feedback`):

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/amazon-ses-sns-subscription.png)

Scroll down and press the _Create subscription_ button. If everything was configured correctly, the subscription should confirm itself and the _Status_ should reflect this after a page refresh.

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/amazon-ses-sns-subscription-confirmed.png)

To complete your Amazon SES configuration, you need to enter your configuration set's name and AWS region in your Mailcoach Mail configuration:

![screenshot](/docs/laravel-mailcoach/v4/images/mail-configuration/amazon-ses-final-mailcoach-mail-config.png)

Your Amazon SES configuration should now be complete, you can go ahead and try sending a test mail. It may go to your spam if you have not set up your domain settings.

Note: If you have created a new Amazon SES account, you will need to request a sending limit change to have your account removed from the sandbox. Until your account is removed from the Amazon SES sandbox you will only be able to send emails to verified email addresses.


## Handling Amazon SES feedback in an existing Laravel app

You should only follow these instructions when you've installed Mailcoach in an existing app.

`laravel-mailcoach-ses-feedback` can handle bounce feedback coming from SES. All e-mail addresses that permanently bounce will be unsubscribed from all lists.

You can install the add-on package via composer:

```bash
composer require spatie/laravel-mailcoach-ses-feedback:^3.0
```

### Adding the webhooks route

You must use this route macro in your route service provider. Do **NOT** apply the `web` group middleware to this route as that would cause an unnecessary session to be started for each webhook call.

You can replace `ses-feedback` with any url you'd like.

```php
Route::sesFeedback('ses-feedback');
```

### Configuring webhooks at Amazon SES

#### Simple Email Service Setup
1. In your AWS Management Console, create a configuration set if you haven't already

![screenshot](/docs/laravel-mailcoach/v4/images/ses-feedback/1.create-configuration-set.png)

2. Add an SNS destination in the Event Destinations and make sure to check the event types you would like to receive

![screenshot](/docs/laravel-mailcoach/v4/images/ses-feedback/2-1-add-destination.png)

![screenshot](/docs/laravel-mailcoach/v4/images/ses-feedback/2-2-add-destination.png)

3. Create a new topic for this destination

![screenshot](/docs/laravel-mailcoach/v4/images/ses-feedback/3-create-new-topic.png)

#### Simple Notification Service Setup

> First, make sure your endpoint is accessible, if you're installing this locally you'll need to share your local environment using `valet share` or a service like `ngrok`

![screenshot](/docs/laravel-mailcoach/v4/images/ses-feedback/4-1-create-subscription.png)

![screenshot](/docs/laravel-mailcoach/v4/images/ses-feedback/4-2-create-subscription.png)

1. Create a subscription for the topic you just created, use `HTTPS` as the Protocol
2. Enter the endpoint you just created the route for
3. Do **not** check "Enable raw message delivery", otherwise signature validation won't work
4. In **Delivery retry policy (HTTP/S)** make sure to set a limit in the **Maximum receive rate** setting, `5` / second is a good default as that is the default php-fpm pool size.
5. You can leave all other settings on their defaults
6. Your subscription should be automatically confirmed if the endpoint was reachable

![screenshot](/docs/laravel-mailcoach/v4/images/ses-feedback/5-subscription-confirmed.png)

### Setting the configuration name in your Laravel app

This package automatically adds the correct `X-Configuration-Set` header for Amazon SES to process feedback. Make sure the name of your configuration set is available under the `mailcoach.ses_feedback.configuration_set` configuration key.

Here's an example for a configuration set that is named `mailcoach`:

```php
// in config/mailcoach.php

'ses_feedback' => [
    'configuration_set' => 'mailcoach',
]
```

### Using the correct mail driver

If you haven't done so already, you must configure Laravel to use the Amazon SES driver. Follow the instruction in [the mail section of the Laravel docs](https://laravel.com/docs/7.x/mail#driver-prerequisites).
