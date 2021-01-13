<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\Concerns\AutomationAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;

class UnsubscribeAction extends AutomationAction
{
    public static function getName(): string
    {
        return __('Unsubscribe');
    }

    public function run(Subscriber $subscriber): void
    {
        $subscriber->unsubscribe();
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
