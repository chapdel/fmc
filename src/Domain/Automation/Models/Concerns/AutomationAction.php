<?php


namespace Spatie\Mailcoach\Domain\Automation\Models\Concerns;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\Concerns\AutomationStep;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;

abstract class AutomationAction extends AutomationStep
{
    public function run(Subscriber $subscriber): void
    {
    }

    public function shouldContinue(Subscriber $subscriber): bool
    {
        return true;
    }

    public function shouldHalt(Subscriber $subscriber): bool
    {
        return false;
    }

    public function store(string $uuid, Automation $automation, ?int $order = null): Action
    {
        return Action::updateOrCreate([
            'uuid' => $uuid,
        ], [
            'automation_id' => $automation->id,
            'order' => $order ?? $automation->actions()->max('order') + 1,
            'action' => $this,
        ]);
    }
}
