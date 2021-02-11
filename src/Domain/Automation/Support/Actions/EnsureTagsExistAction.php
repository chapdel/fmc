<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Carbon\CarbonInterval;
use Illuminate\Support\Str;
use Livewire\CreateBladeView;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;

class EnsureTagsExistAction extends AutomationAction
{
    public function __construct(
        protected CarbonInterval $checkFor,
        protected array $tags,
        protected array $defaultActions = [],
        ?string $uuid = null,
    ) {
        parent::__construct($uuid);
    }

    public static function getName(): string
    {
        return __('Ensure tags exist');
    }

    public function getDescription(): string
    {
        $checkFor = $this->checkFor->forHumans();

        $template = <<<'blade'
            <div>
                <p class="mb-4">Checking for {{ $checkFor }} on the following tags.</p>
                <div class="mb-4">
                    @foreach ($tags as $tag)
                        <strong>{{ $tag['tag'] }}</strong>
                        @foreach ($tag['actions'] as $index => $action)
                            <div class="mb-4">
                                <span>{{ $index + 1 }}. {{ $action['class']::getName() }}</span>
                                @if ($description = $action['class']::make($action['data'])->getDescription())
                                    <span class="px-2 py-1">{{ $description }}</span>
                                @endif
                            </div>
                        @endforeach
                    @endforeach
                </div>
                <div>
                    <strong>Default when no tag matches</strong>
                    @foreach ($defaultActions as $index => $action)
                        <div class="mb-4">
                            <span>{{ $index + 1 }}. {{ $action['class']::getName() }}</span>
                            @if ($description = $action['class']::make($action['data'])->getDescription())
                                <span class="px-2 py-1">{{ $description }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        blade;

        $view = app('view')->make(CreateBladeView::fromString($template), [
            'tags' => $this->tags,
            'checkFor' => $checkFor,
            'defaultActions' => $this->defaultActions,
        ]);

        return $view->render();
    }

    public static function getComponent(): ?string
    {
        return 'ensure-tags-exist-action';
    }

    public function store(string $uuid, Automation $automation, ?int $order = null): Action
    {
        $parent = parent::store($uuid, $automation, $order);

        $newChildrenUuids = collect($this->tags)->flatMap(function ($tag) {
            return $tag['actions'];
        })->pluck('uuid')->merge(collect($this->defaultActions)->pluck('uuid'));

        $parent->children()->each(function (Action $existingChild) use ($newChildrenUuids) {
            if (! $newChildrenUuids->contains($existingChild->uuid)) {
                $existingChild->delete();
            }
        });

        foreach ($this->tags as $tag) {
            foreach ($tag['actions'] as $index => $action) {
                $this->storeChildAction(
                    action: $action,
                    automation: $automation,
                    parent: $parent,
                    key: $tag['tag'],
                    order: $index
                );
            }
        }

        foreach ($this->defaultActions as $index => $action) {
            $this->storeChildAction(
                action: $action,
                automation: $automation,
                parent: $parent,
                key: 'default',
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
            CarbonInterval::createFromDateString($data['checkFor']),
            $data['tags'],
            $data['defaultActions'],
        );
    }

    public function toArray(): array
    {
        return [
            'checkFor' => $this->checkFor->forHumans(),
            'tags' => collect($this->tags)->map(function ($tag) {
                $tag['actions'] = collect($tag['actions'])->map(function ($action) {
                    if (! $action instanceof AutomationAction) {
                        return $action;
                    }

                    return [
                        'uuid' => $action->uuid,
                        'class' => $action::class,
                        'data' => $action->toArray(),
                    ];
                })->toArray();

                return $tag;
            })->toArray(),
            'defaultActions' => collect($this->defaultActions)->map(function ($action) {
                if (! $action instanceof AutomationAction) {
                    return $action;
                }

                return [
                    'uuid' => $action->uuid,
                    'class' => $action::class,
                    'data' => $action->toArray(),
                ];
            })->toArray(),
        ];
    }

    public function shouldContinue(Subscriber $subscriber): bool
    {
        foreach ($this->tags as $tag) {
            if ($subscriber->hasTag($tag['tag'])) {
                return true;
            }
        }

        /** @var \Illuminate\Support\Carbon $addedToActionAt */
        $addedToActionAt = $subscriber->pivot->created_at;

        return $addedToActionAt->add($this->checkFor)->isPast();
    }

    public function nextAction(Subscriber $subscriber): ?Action
    {
        $parentAction = Action::findByUuid($this->uuid);

        foreach ($this->tags as $tag) {
            if ($subscriber->hasTag($tag['tag']) && isset($tag['actions'][0])) {
                return $parentAction->children->where('key', $tag['tag'])->first();
            }
        }

        if (isset($this->defaultActions[0])) {
            return $parentAction->children->where('key', 'default')->first();
        }

        return parent::nextAction($subscriber);
    }
}
