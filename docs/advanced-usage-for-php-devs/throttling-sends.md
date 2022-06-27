---
title: Throttling sends
weight: 2
---

Most email providers have a limit on how many emails you can send within a given amount of time. By default, five mails per second will be sent. In the config file you can customize this behavior by changing the `throttling` key:

```php
'throttling' => [
    'allowed_number_of_jobs_in_timespan' => 10,
    'timespan_in_seconds' => 1,

    /*
     * Throttling relies on the cache. Here you can specify the store to be used.
     *
     * When passing `null`, we'll use the default store.
     */
    'cache_store' => null,
],
```
