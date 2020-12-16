<?php

namespace Spatie\Mailcoach\Support\Automation\Livewire\Components;

use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Enums\CampaignStatus;
use Spatie\Mailcoach\Models\Automation;
use Spatie\Mailcoach\Support\Automation\Livewire\AutomationComponent;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;
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

                <livewire:action-builder :automation="$automation" :actionData="['actions' => $actions]" />
            </div>
        blade;
    }
}
