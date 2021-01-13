<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\Concerns\AutomationAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;

class HaltAction extends AutomationAction
{
    public static function getName(): string
    {
        return __('Halt the automation');
    }

    public function shouldHalt(Subscriber $subscriber): bool
    {
        return true;
    }

    public function shouldContinue(Subscriber $subscriber): bool
    {
        return false;
    }
}
