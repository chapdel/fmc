<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire;

class AutomationActionComponent extends AutomationComponent
{
    public array $action;

    public string $uuid;

    public bool $editing = false;

    public bool $editable = true;

    public bool $deletable = true;

    public int $index = 0;

    public function rules(): array
    {
        return [];
    }

    public function edit()
    {
        $this->editing = true;

        $this->dispatch('editAction', $this->uuid);
    }

    public function save()
    {
        if (! empty($this->rules())) {
            $this->validate();
        }

        $this->dispatch('actionSaved', $this->uuid, $this->getData());

        $this->editing = false;
    }

    public function delete()
    {
        $this->dispatch('actionDeleted', $this->uuid);
    }

    public function getData(): array
    {
        return [];
    }

    public function loadData(): void
    {
        $actionModel = self::getAutomationActionClass()::findByUuid($this->action['uuid']);

        if (! $actionModel) {
            return;
        }

        $this->action['active'] = $actionModel->activeSubscribers()->count();
        $this->action['completed'] = $actionModel->completedSubscribers()->count();
        $this->action['halted'] = $actionModel->haltedSubscribers()->count();
    }

    public function render()
    {
        return view('mailcoach::app.automations.components.automationAction');
    }
}
