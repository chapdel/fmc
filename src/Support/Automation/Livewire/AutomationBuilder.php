<?php

namespace Spatie\Mailcoach\Support\Automation\Livewire;

use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\Livewire;
use Spatie\Mailcoach\Models\Automation;

class AutomationBuilder extends AutomationComponent
{
    public string $name = '';

    public array $actions = [];

    public array $editingActionData = [];

    protected $listeners = ['actionUpdated', 'validationFailed'];

    public function actionUpdated(array $actionData)
    {
        $this->editingActionData = $actionData;
    }

    public function getData(): array
    {
        return [
            'name' => $this->name,
            'actions' => $this->actions,
        ];
    }

    public function render()
    {
        $actionOptions = collect(config('mailcoach.automation.actions'))
            ->flatMap(function (string $action) {
                return [$action => $action::getName()];
            });

        return view('mailcoach::app.automations.components.actionBuilder', [
            'actionOptions' => $actionOptions,
            'actions' => $this->actions,
        ]);
    }

    public function addAction(string $actionClass): void
    {
        $this->actions[] = [
            'editing' => $actionClass::getComponent() ? true : false,
            'class' => $actionClass,
            'data' => [],
        ];

        $this->emitUp('automationBuilderUpdated', $this->getData());
    }

    public function editAction(int $index)
    {
        $this->actions = array_map(function ($action) {
            $action['editing'] = false;
            return $action;
        }, $this->actions);

        $this->actions[$index]['editing'] = true;
        $this->editingActionData = $this->actions[$index]['data'];
    }

    public function saveAction(int $index)
    {
        $data = $this->validateAction($index);

        $this->actions[$index]['data'] = $data;
        $this->actions[$index]['editing'] = false;

        $this->editingActionData = [];

        $this->emitUp('automationBuilderUpdated', $this->getData());
    }

    public function updated($fieldName): void
    {
        $this->resetValidation($fieldName);

        $this->emitUp('automationBuilderUpdated', $this->getData());
    }

    public function removeAction(int $index) {
        unset($this->actions[$index]);

        $this->actions = array_values($this->actions);

        $this->emitUp('automationBuilderUpdated', $this->getData());
    }

    private function validateAction(int $index): array
    {
        $componentClass = $this->actions[$index]['class']::getComponent();
        if (! $componentClass) {
            return [];
        }

        $component = Livewire::getInstance($componentClass, 1);

        $validator = Validator::make(
            $this->editingActionData,
            $component->rules()
        );

        if ($validator->fails()) {
            $this->emitTo($componentClass, 'validationFailed', $validator->errors());
        }

        return $validator->validate();
    }
}
