<?php


namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums\ActionCategoryEnum;
use Spatie\Mailcoach\Domain\Automation\Support\AutomationStep;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

abstract class AutomationAction extends AutomationStep
{
    use UsesMailcoachModels;

    abstract public static function getCategory(): ActionCategoryEnum;

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

    public function duplicate(): static
    {
        $clone = clone $this;
        $clone->uuid = Str::uuid()->toString();

        return $clone;
    }

    public function store(string $uuid, Automation $automation, ?int $order = null, ?int $parent_id = null, ?string $key = null): Action
    {
        $actionClass = static::getAutomationActionClass();

        return $actionClass::updateOrCreate([
            'uuid' => $uuid,
        ], [
            'automation_id' => $automation->id,
            'order' => $order ?? $automation->actions()->max('order') + 1,
            'action' => $this,
            'parent_id' => $parent_id,
            'key' => $key,
        ]);
    }

    /** @return Action[] */
    public function nextActions(Subscriber $subscriber): array
    {
        $actionClass = static::getAutomationActionClass();
        $action = $actionClass::findByUuid($this->uuid);

        if ($action->children->count()) {
            return [$action->children->first()];
        }

        if (! $action->parent_id) {
            return [$action->automation->actions->where('order', '>', $action->order)->first()];
        }

        if ($nextAction = $action->parent->children->where('order', '>', $action->order)->first()) {
            return [$nextAction];
        }

        return [$action->automation->actions->where('order', '>', $action->parent->order)->first()];
    }
}
