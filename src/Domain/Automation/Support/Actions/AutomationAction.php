<?php


namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\AutomationStep;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;

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

    public function nextAction(Subscriber $subscriber): ?Action
    {
        $action = Action::findByUuid($this->uuid);

        if ($action->children->count()) {
            return $action->children->first();
        }

        if (! $action->parent_id) {
            return $action->automation->actions->where('order', '>', $action->order)->first();
        }

        if ($nextAction = $action->parent->children->where('order', '>', $action->order)->first()) {
            return $nextAction;
        }

        return $action->automation->actions->where('order', '>', $action->parent->order)->first();
    }
}
