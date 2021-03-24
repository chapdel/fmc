---
title: Creating Custom Authorization Policies
weight: 7
---

By default, all Mailcoach backend and API actions are protected by the "viewMailcoach" gate. If this is 
sufficient for your use case, then no further action is required. However, you may need more fine-grained 
authorization logic to govern access to different Mailcoach features. In that case, you can create custom 
[authorization policies](https://laravel.com/docs/8.x/authorization#generating-policies).

Policies are resolved via the Laravel service container, so swapping out a default policy for
one of your own is as simple as binding it in a service provider:

```php
use Spatie\Mailcoach\Domain\Audience\Policies\EmailListPolicy;
...
    public function register()
    {
        app()->bind(EmailListPolicy::class, MyFancyCustomListPolicy::class);
    }
...
```

Policies are currently supported for the following model/action combinations:

* EmailList
    * "viewAny"
    * "create"
    * "view"  
    * "update"
    * "delete"
* Campaign
    * "viewAny"
    * "create"
    * "view"
    * "update"
    * "delete"
* Template
    * "viewAny"
    * "create"
    * "view"
    * "update"
    * "delete"
