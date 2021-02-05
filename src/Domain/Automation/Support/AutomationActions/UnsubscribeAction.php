<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\AutomationActions;

use Spatie\Mailcoach\Domain\Automation\Support\AutomationActions\AutomationAction;
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
