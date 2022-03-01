---
title: In an existing Laravel app
weight: 3
---

You can add Mailcoach to your own application that's running in Vapor. On this page, we're only going to provide steps to add Mailcoach to a Laravel app that's already running on Vapor. We are going to assume that you're already familiar with [Laravel Vapor](https://vapor.laravel.com).

By installing Mailcoach inside an existing app, you can integrate Mailcoach into your application logic. You can listen for the various events that Mailcoach fires when mails are opened, clicked, ... to execute custom logic. Mailcoach stores information in the database using regular Eloquent models, that can be used by your application code too.

## Getting a license

In order to install Mailcoach, you'll need to [get a license](/docs/laravel-mailcoach/v5/general/getting-a-license) first.

## Installation via composer

First, add the `satis.spatie.be` repository in your `composer.json`.

```php
"repositories": [
    {
        "type": "composer",
        "url": "https://satis.spatie.be"
    }
],
```

Next, you need to create a file called `auth.json` and place it either next to the `composer.json` file in your project, or in the composer home directory. You can determine the composer home directory on *nix machines by using this command.

```bash
composer config --list --global | grep home
```

This is the content you should put in `auth.json`:

```php
{
    "http-basic": {
        "satis.spatie.be": {
            "username": "<YOUR-SPATIE.BE-ACCOUNT-EMAIL-ADDRESS-HERE>",
            "password": "<YOUR-LICENSE-KEY-HERE>"
        }
    }
}
```

To be sure you can reach `satis.spatie.be` clean your autoloaders before using this command:

```bash
composer dump-autoload
```

Then with the configuration above in place, you'll be able to install the package into your project using this command:

```bash
composer require "spatie/laravel-mailcoach:^4.0"
```

## Publish the config file

Optionally, You can publish the config file with this command.

```bash
php artisan vendor:publish --provider="Spatie\Mailcoach\MailcoachServiceProvider" --tag="mailcoach-config"
```

## Configure an email sending service

Mailcoach can send out mail via various email sending services and can handle the feedback (opens, clicks, bounces, complaints) those services give.

Head over to the Laravel documentation to learn [how to set up a mailer](https://laravel.com/docs/7.x/mail#configuration).

Mailcoach can use different mailers for sending confirmation & welcome mails and campaign mails. This way you can keep very good reputations with the mailer service you use for campaign mails, as the confirmation mails might bounce.

To use different mailers, fill in the name of configured mailers in the `campaigns.mailer` and `transactional.mailer` of the `mailcoach.php` config file.

To configure tracking open, clicks, bounces & complaints, follow the instruction on the dedicated docs page of each supported service.

- [Amazon SES](/docs/laravel-mailcoach/v5/configuring-mail-providers/amazon-ses)
- [Mailgun](/docs/laravel-mailcoach/v5/configuring-mail-providers/mailgun)
- [Sendgrid](/docs/laravel-mailcoach/v5/configuring-mail-providers/sendgrid)
- [Postmark](/docs/laravel-mailcoach/v5/configuring-mail-providers/postmark)

### Configuring vapor.yml

In `vapor.yml` you should set the `id` and `name` keys to the id and name of your Vapor project.

Mailcoach uses a database to store information. Make sure you have a [provisioned a database in Vapor](https://docs.vapor.build/1.0/resources/databases.html#creating-databases) and specify its name in the `database` key. in `vapor.yml`.

Mailcoach uses cache to reliably throttle API calls to email sending services.  You should [provision a cache at Vapor](https://docs.vapor.build/1.0/resources/caches.html#creating-caches) and specify the name of your cache in `vapor.yml`

When you import subscribers via an uploaded CSV, Mailcoach has to write that files somewhere. Make sure to [attach storage to your Vapor app](https://docs.vapor.build/1.0/resources/storage.html).

## Prepare the database

You need to publish the migration:

```bash
php artisan vendor:publish --provider="Spatie\Mailcoach\MailcoachServiceProvider" --tag="mailcoach-migrations"
```

## Add the route macro

You must register the routes needed to handle subscription confirmations, open, and click tracking. We recommend that you don't put this in your routes file, but in the `boot` method of `RouteServiceProvider`

```php
Route::mailcoach('mailcoach');
```

## Schedule the commands

In the console kernel, you should schedule these commands.

Regarding the mailcoach:cleanup-processed-feedback command, be aware that email providers such as SES are a 'deliver at least once' service. Duplicate feedback delivery could be seen weeks after the event. Mailcoach prevents duplicates from SES by checking for old matching feedback. As such, cleaning up historical feedback webhooks could lead to duplicate feedbacks items being processed multiple times. The end result is inflated open and click metrics.

```php
// in app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // ...
    $schedule->command('mailcoach:send-automation-mails')->everyMinute()->withoutOverlapping()->runInBackground();
    $schedule->command('mailcoach:send-scheduled-campaigns')->everyMinute()->withoutOverlapping()->runInBackground();

    $schedule->command('mailcoach:run-automation-triggers')->everyMinute()->runInBackground();
    $schedule->command('mailcoach:run-automation-actions')->everyMinute()->runInBackground();

    $schedule->command('mailcoach:calculate-statistics')->everyMinute();
    $schedule->command('mailcoach:calculate-automation-mail-statistics')->everyMinute();
    $schedule->command('mailcoach:rescue-sending-campaigns')->hourly();
    $schedule->command('mailcoach:send-campaign-summary-mail')->hourly();
    $schedule->command('mailcoach:cleanup-processed-feedback')->hourly();
    $schedule->command('mailcoach:send-email-list-summary-mail')->mondays()->at('9:00');
    $schedule->command('mailcoach:delete-old-unconfirmed-subscribers')->daily();
}
```

## Publish the assets

You must publish the JavaScript and CSS assets using this command:

```bash
php artisan vendor:publish --tag mailcoach-assets --force
```

To ensure that these assets get republished each time Mailcoach is updated, we highly recommend you add the following command to the `post-update-cmd` of the `scripts` section of your `composer.json`.

```php
"scripts": {
    "post-update-cmd": [
        "@php artisan vendor:publish --tag mailcoach-assets --force"
    ]
}
```

## Configure the queues

Mailcoach uses several named queues to send mails and process feedback. In your `vapor.yml`, you should add these queues to all the environment you're deploying Mailcoach to.

```yaml
environments:
  production:
    queues:
      - default
      - mailcoach
      - mailcoach-feedback
      - send-mail
      - send-campaign
      - send-automation-mail
```

## Add authorization to Mailcoach UI

`laravel-mailcoach` does not come with any user management, we assume that you already provide this in your own app. You can use a gate check to determine who can access Mailcoach.

You can determine which users of your application are allowed to view the mailcoach UI by defining a gate check called `viewMailcoach`.

Here's an example where we assume that administrators of your application have a `admin` attribute that is set to `true`. This gate definition will only allow administrators to use the Mailcoach UI.

```php
// in a service provider

public function boot()
{
   \Illuminate\Support\Facades\Gate::define('viewMailcoach', function ($user = null) {
       return optional($user)->admin;
   });
}
```

Mailcoach will redirect unauthorized users to the route specified in the `redirect_unauthorized_users_to_route` key of the Mailcoach config file. By default, the `login` route is used. If you don't have a `login` route in your application, set this setting to a route that does exist.

## Specifying a timezone

By default, all dates in Mailcoach are in UTC. If you want to use another timezone, you can change the `timezone` setting in the `config/app.php` config file.

## Choosing an editor

By default, Mailcoach uses a plain textarea field to edit campaigns and templates. If you'd like to use a feature rich editor, you can [use one of the add-on packages](/docs/laravel-mailcoach/v5/choosing-an-editor/introduction).

## Visit the UI

After performing all these steps, you should be able to visit the Mailcoach UI at `/mailcoach`.

Before sending out a real campaign, we highly recommend creating a small email list with a couple of test email addresses and send a campaign to it. This way, you can verify that sending mails, and the open & click tracking are all working correctly.



