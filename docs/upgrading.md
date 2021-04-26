---
title: Upgrading
weight: 4
---

## From v3 to v4

### Update satis

From now on, Mailcoach can be installed using the Satis installation on the `spatie.be` domain. Using Satis on `mailcoach.app` is not possible anymore.

If you are still using `satis.mailcoach.app` in your `composer.json` file, replace it with `satis.spatie.be`.

### Update your dependencies

These are all the new package versions, make sure any you're using are up to date in your `composer.json`

```json
{
    "spatie/laravel-mailcoach": "^4.0",
    "spatie/laravel-mailcoach-mailgun-feedback": "^3.0",
    "spatie/laravel-mailcoach-monaco": "^2.0",
    "spatie/laravel-mailcoach-postmark-feedback": "^3.0",
    "spatie/laravel-mailcoach-sendgrid-feedback": "^3.0",
    "spatie/laravel-mailcoach-ses-feedback": "^3.0",
    "spatie/laravel-mailcoach-unlayer": "^2.0",
    "spatie/laravel-welcome-notification": "^2.0",
}
```

### Upgrading the database schema

There's been a lot of changes to the database, use the migration below to update your database to the latest schema:

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpgradeMailcoachV3ToV4 extends Migration
{
    public function up()
    {
        Schema::table('mailcoach_campaigns', function (Blueprint $table) {
            $table->boolean('utm_tags')->default(false)->after('track_clicks');
        });
        
        Schema::table('mailcoach_subscribers', function (Blueprint $table) {
            $table->index(['email_list_id', 'created_at'], 'email_list_id_created_at');
            
            // This index might already exist, then you don't need to add it.
            $table->index([
                'email_list_id',
                'subscribed_at',
                'unsubscribed_at'
            ], 'email_list_subscribed_index');
        });

        Schema::create('mailcoach_transactional_mails', function (Blueprint $table) {
            $table->id();

            $table->text('subject');

            $table->json('from');
            $table->json('to');
            $table->json('cc')->nullable();
            $table->json('bcc')->nullable();
            $table->longText('body')->nullable();
            $table->longText('structured_html')->nullable();

            $table->boolean('track_opens')->default(false);
            $table->boolean('track_clicks')->default(false);

            $table->string('mailable_class');

            $table->timestamps();
        });

        Schema::create('mailcoach_automation_mails', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->uuid('uuid');

            $table->string('from_email')->nullable();
            $table->string('from_name')->nullable();

            $table->string('reply_to_email')->nullable();
            $table->string('reply_to_name')->nullable();

            $table->string('subject')->nullable();

            $table->longText('html')->nullable();
            $table->longText('structured_html')->nullable();
            $table->longText('email_html')->nullable();
            $table->longText('webview_html')->nullable();

            $table->string('mailable_class')->nullable();
            $table->json('mailable_arguments')->nullable();

            $table->boolean('track_opens')->default(false);
            $table->boolean('track_clicks')->default(false);
            $table->boolean('utm_tags')->default(false);
            
            $table->integer('sent_to_number_of_subscribers')->default(0);
            $table->integer('open_count')->default(0);
            $table->integer('unique_open_count')->default(0);
            $table->integer('open_rate')->default(0);
            $table->integer('click_count')->default(0);
            $table->integer('unique_click_count')->default(0);
            $table->integer('click_rate')->default(0);
            $table->integer('unsubscribe_count')->default(0);
            $table->integer('unsubscribe_rate')->default(0);
            $table->integer('bounce_count')->default(0);
            $table->integer('bounce_rate')->default(0);
            $table->timestamp('statistics_calculated_at')->nullable();

            $table->timestamp('last_modified_at')->nullable();
            $table->timestamps();
        });

        Schema::table('mailcoach_sends', function (Blueprint $table) {
            $table
                ->foreignId('campaign_id')
                ->nullable()
                ->change();
                
            $table
                ->foreignId('subscriber_id')
                ->nullable()
                ->change();
            
            $table
                ->foreignId('automation_mail_id')
                ->nullable()
                ->constrained('mailcoach_automation_mails')
                ->cascadeOnDelete()
                ->after('campaign_id');

            $table
                ->foreignId('transactional_mail_id')
                ->nullable()
                ->constrained('mailcoach_transactional_mails')
                ->cascadeOnDelete()
                ->after('automation_mail_id');
        });

        Schema::table('mailcoach_subscriber_imports', function (Blueprint $table) {
            $table->boolean('replace_tags')->default(false)->after('unsubscribe_others');
        });

        Schema::table('mailcoach_tags', function (Blueprint $table) {
            $table->string('type')->default('default')->after('name');
        });

        Schema::create('mailcoach_automations', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('email_list_id')
                ->nullable()
                ->constrained('mailcoach_email_lists')
                ->cascadeOnDelete();

            $table->uuid('uuid');
            $table->string('name')->nullable();
            $table->string('interval')->nullable();
            $table->string('status');

            $table->text('segment_class')->nullable();

            $table
                ->foreignId('segment_id')
                ->nullable()
                ->constrained('mailcoach_segments')
                ->nullOnDelete();

            $table->string('segment_description')->default(0);

            $table->timestamp('run_at')->nullable();
            $table->timestamp('last_ran_at')->nullable();

            $table->timestamps();
        });

        Schema::create('mailcoach_automation_actions', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('automation_id')
                ->nullable()
                ->constrained('mailcoach_automations')
                ->cascadeOnDelete();

            $table
                ->foreignId('parent_id')
                ->nullable()
                ->constrained('mailcoach_automation_actions')
                ->cascadeOnDelete();

            $table->uuid('uuid');
            $table->string('key')->nullable();
            $table->text('action')->nullable();
            $table->integer('order');
            $table->timestamps();
        });
        
        Schema::create('mailcoach_automation_triggers', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('automation_id')
                ->nullable()
                ->constrained('mailcoach_automations')
                ->cascadeOnDelete();

            $table->uuid('uuid');
            $table->text('trigger')->nullable();
            $table->timestamps();
        });

        Schema::create('mailcoach_automation_action_subscriber', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('action_id');
            $table->unsignedBigInteger('subscriber_id');
            $table->timestamp('run_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('halted_at')->nullable();
            $table->timestamps();

            $table
                ->foreign('action_id')
                ->references('id')->on('mailcoach_automation_actions')
                ->onDelete('cascade');

            $table
                ->foreign('subscriber_id')
                ->references('id')->on('mailcoach_subscribers')
                ->onDelete('cascade');
        });

        Schema::create('mailcoach_automation_mail_opens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('send_id')->constrained('mailcoach_sends');

            $table
                ->foreignId('subscriber_id')
                ->nullable()
                ->constrained('mailcoach_subscribers')
                ->cascadeOnDelete();

            $table
                ->foreignId('automation_mail_id')
                ->nullable()
                ->constrained('mailcoach_automation_mails')
                ->cascadeOnDelete();

            $table->timestamps();
        });
        
        Schema::create('mailcoach_automation_mail_links', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('automation_mail_id')
                ->constrained('mailcoach_automation_mails')
                ->cascadeOnDelete();

            $table->string('url', 2048);
            $table->integer('click_count')->default(0);
            $table->integer('unique_click_count')->default(0);
            $table->nullableTimestamps();
        });

        Schema::create('mailcoach_automation_mail_clicks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('send_id')->constrained('mailcoach_sends');
            $table->foreignId('automation_mail_link_id')->constrained('mailcoach_automation_mail_links');

            $table
                ->foreignId('subscriber_id')
                ->nullable()
                ->constrained('mailcoach_subscribers')
                ->cascadeOnDelete();

            $table->timestamps();
        });

        Schema::create('mailcoach_automation_mail_unsubscribes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('automation_mail_id');

            $table
                ->foreign('automation_mail_id', 'auto_unsub_automation_mail_id')
                ->references('id')->on('mailcoach_automation_mails')
                ->cascadeOnDelete();

            $table
                ->foreignId('subscriber_id')
                ->constrained('mailcoach_subscribers')
                ->cascadeOnDelete();

            $table->timestamps();
        });

        Schema::create('mailcoach_transactional_mail_opens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('send_id')->constrained('mailcoach_sends');

            $table->timestamps();
        });

        Schema::create('mailcoach_transactional_mail_clicks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('send_id')->constrained('mailcoach_sends');
            $table->longText('url');

            $table->timestamps();
        });

        Schema::create('mailcoach_transactional_mail_templates', function (Blueprint $table) {
            $table->id();
            $table->json('cc')->nullable();
            $table->string('label')->nullable();
            $table->string('name');
            $table->string('subject')->nullable();
            $table->text('from')->nullable();
            $table->json('to')->nullable();
            $table->json('bcc')->nullable();
            $table->longText('structured_html')->nullable();
            $table->longText('body')->nullable();
            $table->string('type'); // html, blade, markdown
            $table->json('replacers')->nullable();
            $table->boolean('store_mail')->default(false);
            $table->boolean('track_opens')->default(false);
            $table->boolean('track_clicks')->default(false);
            $table->text('test_using_mailable')->nullable();
            $table->timestamps();
        });
    }
}
```

You'll notice that the migration contains a few `change()` calls. In order to run the migration you'll need to install the `doctrine/dbal` package, like instructed in [the Laravel docs](https://laravel.com/docs/master/migrations#modifying-columns).

### Config file changes

The `mailcoach.php` config file has changed significantly. We recommend renaming the `mailcoach.php` config file, so you can still reference it. 

Publish the new config file using

```bash
php artisan vendor:publish --tag=mailcoach-config
```

Make sure to bring over any customizations you did to the old config file. After you're done, you can delete the old, renamed config file.

#### Sanctum auth

Make sure the api middleware config contains `auth:sanctum` as seen here https://github.com/spatie/Mailcoach/blob/main/config/mailcoach.php#L255-L258

#### Horizon config

This is the new recommended horizon config, the only real change is the addition of `send-automation-mail`:

```php
'mailcoach-general' => [
    'connection' => 'mailcoach-redis',
    'queue' => ['mailcoach', 'mailcoach-feedback', 'send-mail', 'send-automation-mail'],
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
```

### View changes

If you had customized views, you'll need to reapply your own customizations to the new views.

Publish the new views using

```bash
php artisan vendor:publish --tag=mailcoach-views
```

### Namespace changes

Most namespaces have been changed to a new Domain based structure separated into `Audience`, `Campaign`, `Automation`, `TransactionalMail` and `Shared`.

If you're using any of the Mailcoach classes in your own project, make sure to validate the namespace imports. Below are some of the most impactful old namespaces and their resulting namespace:

#### Audience
- `\Spatie\Mailcoach\Models\Subscriber` has been moved to `\Spatie\Mailcoach\Domain\Audience\Models\Subscriber`
- `\Spatie\Mailcoach\Models\EmailList` has been moved to `\Spatie\Mailcoach\Domain\Audience\Models\EmailList`
- `\Spatie\Mailcoach\Models\Tag` has been moved to `\Spatie\Mailcoach\Domain\Audience\Models\Tag`
- `\Spatie\Mailcoach\Models\TagSegment` has been moved to `\Spatie\Mailcoach\Domain\Audience\Models\TagSegment`

- All Subscriber actions were moved from `\Spatie\Mailcoach\Actions\Subscribers` to `\Spatie\Mailcoach\Domain\Audience\Actions\Subscribers`
- All EmailList actions were moved from `\Spatie\Mailcoach\Actions\EmailLists` to `\Spatie\Mailcoach\Domain\Audience\Actions\EmailLists`

#### Campaigns
- `\Spatie\Mailcoach\Models\Campaign` has been moved to `\Spatie\Mailcoach\Domain\Campaign\Models\Campaign`
- `\Spatie\Mailcoach\Models\CampaignClick` has been moved to `\Spatie\Mailcoach\Domain\Campaign\Models\CampaignClick`
- `\Spatie\Mailcoach\Models\CampaignLink` has been moved to `\Spatie\Mailcoach\Domain\Campaign\Models\CampaignLink`
- `\Spatie\Mailcoach\Models\CampaignOpen` has been moved to `\Spatie\Mailcoach\Domain\Campaign\Models\CampaignOpen`
- `\Spatie\Mailcoach\Models\CampaignUnsubscribe` has been moved to `\Spatie\Mailcoach\Domain\Campaign\Models\CampaignUnsubscribe`
- `\Spatie\Mailcoach\Models\Template` has been moved to `\Spatie\Mailcoach\Domain\Campaign\Models\Template`

- `\Spatie\Mailcoach\Enums\CampaignStatus` has been moved to `\Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus`

- All Campaign actions were moved from `\Spatie\Mailcoach\Actions\Campaigns` to `\Spatie\Mailcoach\Domain\Campaign\Actions`

#### Segments

If you have campaigns with existing segmentation, you can use the following Artisan command in your `routes/console.php` file to migrate those namespaces automatically:

```php
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

Artisan::command('migrate-mailcoach', function () {
    Campaign::each(function (Campaign $campaign) {
        if ($campaign->segment_class === 'Spatie\Mailcoach\Support\Segments\SubscribersWithTagsSegment') {
            $campaign->update([
                'segment_class' => 'Spatie\Mailcoach\Domain\Audience\Support\Segments\SubscribersWithTagsSegment',
            ]);
        }

        if ($campaign->segment_class === 'Spatie\Mailcoach\Support\Segments\EverySubscriberSegment') {
            $campaign->update([
                'segment_class' => 'Spatie\Mailcoach\Domain\Audience\Support\Segments\EverySubscriberSegment',
            ]);
        }
    });
});
```

You can then run `php artisan migrate-mailcoach` to run the command.

### Scheduled jobs

Add these new scheduled jobs to your application's schedule:

```php
$schedule->command('mailcoach:run-automation-triggers')->everyMinute()->runInBackground();
$schedule->command('mailcoach:run-automation-actions')->everyMinute()->runInBackground();
$schedule->command('mailcoach:calculate-automation-mail-statistics')->everyMinute();
```

### Automation mail queue

Make sure to add the `send-automation-mail`, queue to the `mailcoach-general` key in your `horizon.php` config file.

```
'mailcoach-general' => [
    'connection' => 'mailcoach-redis',
    'queue' => ['mailcoach', 'mailcoach-feedback', 'send-mail', 'send-automation-mail'],
    'balance' => 'auto',
    'processes' => 10,
    'tries' => 2,
    'timeout' => 60 * 60,
],
```

## From v2 to v3

### Laravel 8

Mailcoach v3 requires Laravel 8, make sure to [upgrade your project](https://laravel.com/docs/8.x/upgrade#upgrade-8.0) first.

Mailcoach uses job batching under the hood. Make sure you add the required database table, as [mentioned in the Laravel docs on Job batching](https://laravel.com/docs/8.x/queues#job-batching).

### Upgrading the database schema

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

### Upgrading database content

- `open_rate`, `click_rate`, `bounce_rate`, `unsubscribe_rate` of the `mailcoach_campaigs` table: v3 of mailcoach now assumes that the two last numbers are the digits. For campaigns that were sent using v2 you should add two zeroes, so `31` should become `3100`
- `webhook_calls` need the `processed_at` column filled in, you can set this using `update webhook_calls set processed_at = NOW() where processed_at is null;`

### Updating the config file

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

### Horizon configuration

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

### New command for cleanup

We've added a new command for cleanup of processed feedback in the `webhook_calls` table, make sure to add this to your `\App\Console\Kernel` schedule.

Be aware that email providers such as SES are a 'deliver at least once' service. Duplicate feedback delivery could be seen weeks after the event. Mailcoach prevents duplicates from SES by checking for old matching feedback. As such, cleaning up historical feedback webhooks could lead to duplicate feedbacks items being processed multiple times. The end result is inflated open and click metrics.

```php
$schedule->command('mailcoach:cleanup-processed-feedback')->hourly();
```


