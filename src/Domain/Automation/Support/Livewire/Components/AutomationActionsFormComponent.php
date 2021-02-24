<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Components;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class AutomationActionsFormComponent extends Component
{
    use UsesMailcoachModels;

    protected $listeners = ['automationBuilderUpdated', 'editAction', 'actionSaved', 'actionDeleted'];

    public Automation $automation;

    public array $editingActions = [];

    public array $actions;

    public function editAction(string $uuid)
    {
        $this->editingActions[] = $uuid;
    }

    public function actionSaved(string $uuid)
    {
        $actions = array_filter($this->editingActions, function ($actionUuid) use ($uuid) {
            return $actionUuid !== $uuid;
        });

        $this->editingActions = $actions;
    }

    public function actionDeleted(string $uuid)
    {
        $actions = array_filter($this->editingActions, function ($actionUuid) use ($uuid) {
            return $actionUuid !== $uuid;
        });

        $this->editingActions = $actions;
    }

    public function render()
    {
        return view('mailcoach::app.automations.partials.actionsForm');
    }

    public function automationBuilderUpdated($data)
    {
        $this->actions = $data['actions'];
    }
}
