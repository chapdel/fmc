<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions;

use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationActionComponent;

class WaitActionComponent extends AutomationActionComponent
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

    public function getData(): array
    {
        return [
            'length' => (int) $this->length,
            'unit' => $this->unit,
        ];
    }

    public function rules(): array
    {
        return [
            'length' => ['required', 'integer', 'min:1'],
            'unit' => ['required', Rule::in([
                'minutes',
                'hours',
                'days',
                'weeks',
                'months',
            ])],
        ];
    }

    public function render()
    {
        return view('mailcoach::app.automations.components.actions.waitAction');
    }
}
