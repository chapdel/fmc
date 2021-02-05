<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Triggers;

class NoTrigger extends AutomationTrigger
{
    public static function getName(): string
    {
        return __('No trigger');
    }
}
