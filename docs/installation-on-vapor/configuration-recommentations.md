---
title: Configuration recommendations
weight: 4
---

Mailcoach will send each mail in a separate job that has its own database connection. We highly recommend make sure that your database can handle at least as many database connections possible connections as the allowed number of jobs that can happen in a second.

So, if you have this configuration in `mailcoach.php`...

```php
'allowed_number_of_jobs_in_timespan' => 30,
'timespan_in_seconds' => 1,
```

... your database should be able to handle at least 30 connection.

You can find out your connection limit by executing this query

```sql
SHOW VARIABLES LIKE "max_connections"
```

## Uploads on Vapor

To have image uploads work for the editors and have uploads work for subscriber imports, make sure to change these configurations:

Make sure you have an S3 disk configuration that contains `'visibility' => 'public'`, for example:

```php
// config/filesystems.php

'media' => [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION'),
    'bucket' => env('AWS_BUCKET'),
    'url' => env('AWS_URL'),
    'endpoint' => env('AWS_ENDPOINT'),
    'visibility' => 'public', // <-- This line is very important for image uploads to work
],
```

Then, make sure to set the `disk_name` configuration value to the name of your disk for Unlayer and/or the Editor.js editor, in this case `media`

```php
// config/mailcoach.php

'unlayer' => [
    'disk_name' => 'media'
],

'mailcoach-editor' => [
    'disk_name' => 'media'
],
```

For subscriber imports, make sure to set the `mailcoach.audience.import_subscribers_disk` to an S3 filesystem as well:

```php
// config/mailcoach.php

'audience' => [
    ...

    /*
     * This disk will be used to store files regarding importing subscribers.
     */
    'import_subscribers_disk' => 'media',
],
```
