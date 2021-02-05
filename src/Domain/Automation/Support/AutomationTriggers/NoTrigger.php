<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\AutomationTriggers;

use Spatie\Mailcoach\Domain\Automation\Support\AutomationTriggers\AutomationTrigger;

class NoTrigger extends AutomationTrigger
{
    public static function getName(): string
    {
        return __('No trigger');
    }
}
