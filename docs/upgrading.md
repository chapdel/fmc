---
title: Upgrading
weight: 4
---

## Upgrading to v3

### Laravel 8

Mailcoach v3 requires Laravel 8, make sure to [upgrade your project](https://laravel.com/docs/8.x/upgrade#upgrade-8.0) first.

Mailcoach uses job batching under the hood. Make sure you add the required database table, as [mentioned in the Laravel docs on Job batching](https://laravel.com/docs/8.x/queues#job-batching).

## Upgrading the database schema

In your database you should add a few columns. You can add them manually like described below, or use the migration mentioned in [this comment on GitHub](https://github.com/spatie/mailcoach-support/issues/251#issuecomment-700925757).

#### mailcoach_campaigns

- `all_jobs_added_to_batch_at`: timestamp, nullable
- `send_batch_id`: string, nullable
- `reply_to_email`: string, nullable
- `reply_to_name`: string, nullable

#### mailcoach_subscribers

- `imported_via_import_uuid`: uuid, nullable

#### mailcoach_subscriber_imports

- `subscribe_unsubscribed` : boolean, default: false
- `unsubscribe_others`: boolean, default false,

#### mailcoach_email_lists

- `default_reply_to_email`: string, nullable
- `default_reply_to_name`: string, nullable
- `allowed_form_extra_attributes`: text, nullable

#### mailcoach_sends

- add an index on `uuid`

#### webhook_calls

- `processed_at`: timestamp, nullable.
- `external_id`: string, nullable. Make sure to add an index for performance.

## Upgrading database content

- `open_rate`, `click_rate`, `bounce_rate`, `unsubscribe_rate` of the `mailcoach_campaigs` table: v3 of mailcoach now assumes that the two last numbers are the digits. For campaigns that were sent using v2 you should add two zeroes, so `31` should become `3100`
- `webhook_calls` need the `processed_at` column filled in, you can set this using `update webhook_calls set processed_at = NOW() where processed_at is null;`

## Updating the config file

The `middleware` option now contains an array with `web` and `api`. This is the new default.

If you don't have a `middleware` key in your config file, you don't need to do anything as the default will be used. If you do have a `middleware` key, update it accordingly.

```php
    /*
     *  These middleware will be assigned to every Mailcoach routes, giving you the chance
     *  to add your own middleware to this stack or override any of the existing middleware.
     */
    'middleware' => [
        'web' => [
            'web',
            Spatie\Mailcoach\Http\App\Middleware\Authenticate::class,
            Spatie\Mailcoach\Http\App\Middleware\Authorize::class,
            Spatie\Mailcoach\Http\App\Middleware\SetMailcoachDefaults::class,
        ],
        'api' => [
            'api',
            'auth:api',
        ],
    ],
```

## Horizon configuration

We now suggest a new horizon configuration for balancing the queue that Mailcoach uses, make sure `mailcoach-general` and `mailcoach-heavy` are present in your production and local Horizon environments:

```php
// config/horizon.php
'environments' => [
    'production' => [
        'supervisor-1' => [
            'connection' => 'redis',
            'queue' => ['default'],
            'balance' => 'simple',
            'processes' => 10,
            'tries' => 2,
            'timeout' => 60 * 60,
        ],
        'mailcoach-general' => [
            'connection' => 'mailcoach-redis',
            'queue' => ['mailcoach', 'mailcoach-feedback', 'send-mail'],
            'balance' => 'auto',
            'processes' => 10,
            'tries' => 2,
            'timeout' => 60 * 60,
        ],
        'mailcoach-heavy' => [
            'connection' => 'mailcoach-redis',
            'queue' => ['send-campaign'],
            'balance' => 'auto',
            'processes' => 3,
            'tries' => 1,
            'timeout' => 60 * 60,
        ],
    ],

    'local' => [
        'supervisor-1' => [
            'connection' => 'redis',
            'queue' => ['default'],
            'balance' => 'simple',
            'processes' => 10,
            'tries' => 2,
            'timeout' => 60 * 60,
        ],
        'mailcoach-general' => [
            'connection' => 'mailcoach-redis',
            'queue' => ['mailcoach', 'mailcoach-feedback', 'send-mail'],
            'balance' => 'auto',
            'processes' => 10,
            'tries' => 2,
            'timeout' => 60 * 60,
        ],
        'mailcoach-heavy' => [
            'connection' => 'mailcoach-redis',
            'queue' => ['send-campaign'],
            'balance' => 'auto',
            'processes' => 3,
            'tries' => 1,
            'timeout' => 60 * 60,
        ],
    ],
],
```

## New command for cleanup

We've added a new command for cleanup of processed feedback in the `webhook_calls` table, make sure to add this to your `\App\Console\Kernel` schedule.

Be aware that email providers such as SES are a 'deliver at least once' service. Duplicate feedback delivery could be seen weeks after the event. Mailcoach prevents duplicates from SES by checking for old matching feedback. As such, cleaning up historical feedback webhooks could lead to duplicate feedbacks items being processed multiple times. The end result is inflated open and click metrics.

```php
$schedule->command('mailcoach:cleanup-processed-feedback')->hourly();
```

