<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Components;

use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationComponent;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\ValidationRules\Rules\Delimited;

class TagChainComponent extends Component
{
    public Automation $automation;

    public int $index = -1;

    public string $tag = '';

    public array $actions = [];

    protected $listeners = ['automationBuilderUpdated'];

    public function mount(array $initial)
    {
        $this->tag = $initial['tag'] ?? '';
        $this->actions = $initial['actions'] ?? [];
    }

    public function getData(): array
    {
        return [
            'tag' => $this->tag,
            'actions' => $this->actions,
        ];
    }

    public function automationBuilderUpdated(array $data)
    {
        $this->actions = $data['actions'];

        $this->emitUp('tagChainUpdated', ['index' => $this->index] + $this->getData());
    }

    public function rules(): array
    {
        return [
            'tag' => ['required', 'string'],
            'actions' => ['required', 'array'],
        ];
    }

    public function updated(): void
    {
        $this->emitUp('tagChainUpdated', ['index' => $this->index] + $this->getData());
    }

    public function render()
    {
        return <<<'blade'
            <div>
                <div class="mb-4">
                    <x-mailcoach::text-field
                        :label="__('Tag')"
                        name="tag"
                        wire:model="tag"
                    />
                </div>

                <livewire:automation-builder :automation="$automation" :actionData="['actions' => $actions]" />
            </div>
        blade;
    }
}
