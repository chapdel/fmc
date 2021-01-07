<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Triggers;

use Spatie\Mailcoach\Domain\Automation\Models\Concerns\AutomationTrigger;

class NoTrigger extends AutomationTrigger
{
    public static function getName(): string
    {
        return __('No trigger');
    }
}
