<?php

namespace Spatie\Mailcoach\Support\Automation\Actions;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Models\Concerns\AutomationAction;
use Spatie\Mailcoach\Models\Subscriber;

class HaltAction extends AutomationAction
{
    public static function getName(): string
    {
        return __('Halt the automation');
    }

    public static function make(array $data): self
    {
        return new self();
    }

    public static function createFromRequest(Request $request): AutomationAction
    {
        return new self();
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
