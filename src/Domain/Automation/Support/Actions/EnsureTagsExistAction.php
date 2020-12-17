<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Blade;
use Livewire\CreateBladeView;
use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\Concerns\AutomationAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;

class EnsureTagsExistAction extends AutomationAction
{
    public function __construct(
        private CarbonInterval $checkFor,
        private array $tags,
        private array $defaultActions = [],
    ) {}

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
                                    <span class="bg-gray-100 px-2 py-1">{{ $description }}</span>
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
                                <span class="bg-gray-100 px-2 py-1">{{ $description }}</span>
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

    private function storeChildAction($action, Automation $automation, Action $parent, string $key, int $order): Action
    {
        $uuid = $action['uuid'];

        if (! $action instanceof AutomationAction) {
            $action = $action['class']::make($action['data']);
        }

        return Action::updateOrCreate([
            'uuid' => $uuid,
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
            'tags' => $this->tags,
            'defaultActions' => $this->defaultActions,
        ];
    }

    public function shouldHalt(Subscriber $subscriber): bool
    {
        return false; // TODO
        return $this->haltIfDoesntExist && ! $subscriber->hasTag($this->tag);
    }

    public function shouldContinue(Subscriber $subscriber): bool
    {
        return true; // TODO
        return $subscriber->hasTag($this->tag);
    }
}
