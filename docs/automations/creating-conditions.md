---
title: Creating conditions
weight: 4
---

Mailcoach ships with a `ConditionAction` that allows you to define a condition and split the automation in a `true` and `false` branch.

By default this action ships with 3 conditions:

- Subscriber has a specific tag
- Subscriber has opened an automation mail
- Subscriber has clicked (one or all) links in an automation mail

You can also create your own conditions by creating a class that implements the `Spatie\Mailcoach\Domain\Automation\Support\Conditions\Condition` interface.

Let's take a look at the `HasTagCondition` class as an example:

```php
<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Conditions;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;

class HasTagCondition implements Condition
{
    public function __construct(
        private Automation $automation,
        private Subscriber $subscriber,
        private array $data,
    ) {
    }

    public static function getName(): string
    {
        return (string) __('Has tag');
    }

    public static function getDescription(array $data): string
    {
        return (string) __(':tag', ['tag' => $data['tag']]);
    }

    public static function rules(): array
    {
        return [
            'tag' => 'required',
        ];
    }

    public function check(): bool
    {
        return $this->subscriber->hasTag($this->data['tag']);
    }
}
```

### __construct

The construct method receives the current `automation`, the `subscriber` you're checking on and an array of data.

The array of data is only used by the default conditions, custom conditions will always receive an empty array.

### getName & getDescription

The static `getName()` method gets called to show the name of the condition in the dropdown. The `getDescription()` method gets shown on the summary of the action.

### check

The `check()` method is the heart of your condition. Anything is possible in this method, in the example above we're simply checking if the subscriber has a specific tag.
