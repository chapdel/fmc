<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasClickedAutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasOpenedAutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasTagCondition;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationActionComponent;

class ConditionActionComponent extends AutomationActionComponent
{
    public string $length = '1';

    public string $unit = 'days';

    public array $units = [
        'minutes' => 'Minute',
        'hours' => 'Hour',
        'days' => 'Day',
        'weeks' => 'Week',
        'months' => 'Month',
    ];

    public array $yesActions = [];

    public array $noActions = [];

    public string $condition = '';

    public array $conditionOptions = [];

    public array $conditionData = [];

    protected $listeners = ['automationBuilderUpdated'];

    public function getData(): array
    {
        return [
            'length' => (int) $this->length,
            'unit' => $this->unit,
            'condition' => $this->condition,
            'conditionData' => $this->conditionData,
            'yesActions' => $this->yesActions,
            'noActions' => $this->noActions,
        ];
    }

    public function updatedCondition()
    {
        /** @var \Spatie\Mailcoach\Domain\Automation\Support\Conditions\Condition $condition */
        $condition = $this->condition;
        foreach (array_keys($condition::rules()) as $key) {
            if (! isset($this->conditionData[$key])) {
                $this->conditionData[$key] = '';
            }
        }
    }

    public function mount()
    {
        $this->conditionOptions = collect([
            HasTagCondition::class,
            HasOpenedAutomationMail::class,
            HasClickedAutomationMail::class,
        ])->mapWithKeys(function ($class) {
            return [$class => $class::getName()];
        })->toArray();
    }

    public function automationBuilderUpdated(array $data): void
    {
        if (! Str::startsWith($data['name'], $this->uuid)) {
            return;
        }

        if ($data['name'] === $this->uuid . '-yes-actions') {
            $this->yesActions = $data['actions'];
        }

        if ($data['name'] === $this->uuid . '-no-actions') {
            $this->noActions = $data['actions'];
        }

        $this->emitUp('actionUpdated', $this->getData());
    }

    public function rules(): array
    {
        $rules = [
            'length' => ['required', 'integer', 'min:1'],
            'unit' => ['required', Rule::in([
                'minutes',
                'hours',
                'days',
                'weeks',
                'months',
            ])],
            'condition' => ['required'],
            'conditionData' => ['required', 'array'],
            'yesActions' => ['nullable', 'array'],
            'noActions' => ['nullable', 'array'],
        ];

        $conditionRules = collect($this->condition ? $this->condition::rules() : [])->mapWithKeys(function ($rules, $key) {
            return ["conditionData.{$key}" => $rules];
        })->toArray();

        return array_merge($rules, $conditionRules);
    }

    public function render()
    {
        return view('mailcoach::app.automations.components.actions.conditionAction');
    }
}
