<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Components;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class AutomationActionsComponent extends Component
{
    use UsesMailcoachModels;

    protected $listeners = ['automationBuilderUpdated'];

    public Automation $automation;

    public $editing = false;

    public array $actions;

    public function render()
    {
        return view('mailcoach::app.automations.partials.actionsForm');
    }

    public function automationBuilderUpdated($data)
    {
        $this->actions = $data['actions'];
        $this->editing = collect($this->actions)
            ->filter(fn ($action) => $action['editing'])
            ->count() > 0;
    }
}
