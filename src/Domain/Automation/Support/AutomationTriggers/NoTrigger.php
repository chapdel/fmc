<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\AutomationTriggers;

class NoTrigger extends AutomationTrigger
{
    public static function getName(): string
    {
        return __('No trigger');
    }
}
