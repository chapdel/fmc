<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Carbon\CarbonInterval;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums\ActionCategoryEnum;

class ConditionAction extends AutomationAction
{
    public function __construct(
        protected CarbonInterval $checkFor,
        protected array $yesActions = [],
        protected array $noActions = [],
        protected string $condition = '',
        protected array $conditionData = [],
        ?string $uuid = null,
    ) {
        parent::__construct($uuid);
    }

    public static function getCategory(): ActionCategoryEnum
    {
        return ActionCategoryEnum::check();
    }

    public static function getName(): string
    {
        return (string) __('Yes/No condition');
    }

    public static function getComponent(): ?string
    {
        return 'condition-action';
    }

    public function store(string $uuid, Automation $automation, ?int $order = null): Action
    {
        $parent = parent::store($uuid, $automation, $order);

        $newChildrenUuids = collect($this->yesActions)->pluck('uuid')
            ->merge(collect($this->noActions)->pluck('uuid'));

        $parent->children()->each(function (Action $existingChild) use ($newChildrenUuids) {
            if (! $newChildrenUuids->contains($existingChild->uuid)) {
                $existingChild->delete();
            }
        });

        foreach ($this->yesActions as $index => $action) {
            $this->storeChildAction(
                action: $action,
                automation: $automation,
                parent: $parent,
                key: 'yesActions',
                order: $index
            );
        }

        foreach ($this->noActions as $index => $action) {
            $this->storeChildAction(
                action: $action,
                automation: $automation,
                parent: $parent,
                key: 'noActions',
                order: $index
            );
        }

        return $parent;
    }

    protected function storeChildAction($action, Automation $automation, Action $parent, string $key, int $order): Action
    {
        if (! $action instanceof AutomationAction) {
            $uuid = $action['uuid'];
            $action = $action['class']::make($action['data']);
        }

        return Action::updateOrCreate([
            'uuid' => $uuid ?? Str::uuid()->toString(),
        ], [
            'automation_id' => $automation->id,
            'parent_id' => $parent->id,
            'key' => $key,
            'order' => $order,
            'action' => $action,
        ]);
    }

    public static function make(array $data): self
    {
        return new self(
            CarbonInterval::createFromDateString("{$data['length']} {$data['unit']}"),
            $data['yesActions'],
            $data['noActions'],
            $data['condition'],
            $data['conditionData'],
        );
    }

    public function toArray(): array
    {
        [$length, $unit] = explode(' ', $this->checkFor->forHumans());

        return [
            'length' => $length,
            'unit' => $unit,
            'condition' => $this->condition,
            'conditionData' => $this->conditionData,
            'yesActions' => collect($this->yesActions)->map(function ($action) {
                return $this->actionToArray($action);
            })->toArray(),
            'noActions' => collect($this->yesActions)->map(function ($action) {
                return $this->actionToArray($action);
            })->toArray(),
        ];
    }

    private function actionToArray(array | AutomationAction $action): array
    {
        if (! $action instanceof AutomationAction) {
            return $action;
        }

        return [
            'uuid' => $action->uuid,
            'class' => $action::class,
            'data' => $action->toArray(),
        ];
    }

    public function shouldContinue(Subscriber $subscriber): bool
    {
        /** @var \Spatie\Mailcoach\Domain\Automation\Support\Conditions\Condition $condition */
        $conditionClass = $this->condition;
        $condition = new $conditionClass($subscriber, $this->conditionData);

        if ($condition->check()) {
            return true;
        }

        /** @var \Illuminate\Support\Carbon $addedToActionAt */
        $addedToActionAt = $subscriber->pivot->created_at;

        return $addedToActionAt->add($this->checkFor)->isPast();
    }

    public function nextAction(Subscriber $subscriber): ?Action
    {
        $parentAction = Action::findByUuid($this->uuid);

        /** @var \Spatie\Mailcoach\Domain\Automation\Support\Conditions\Condition $condition */
        $conditionClass = $this->condition;
        $condition = new $conditionClass($subscriber, $this->conditionData);

        if ($condition->check()) {
            if (isset($this->yesActions[0])) {
                return $parentAction->children->where('key', 'yesActions')->first();
            }
        } else {
            if (isset($this->noActions[0])) {
                return $parentAction->children->where('key', 'noActions')->first();
            }
        }

        return parent::nextAction($subscriber);
    }
}
