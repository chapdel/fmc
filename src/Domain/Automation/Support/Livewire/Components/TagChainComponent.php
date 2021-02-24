<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Components;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;

class TagChainComponent extends Component
{
    public Automation $automation;

    public int $index = -1;

    public string $tag = '';

    public array $actions = [];

    protected $listeners = ['automationBuilderUpdated'];

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
        return view('mailcoach::app.automations.components.tagChain');
    }
}
