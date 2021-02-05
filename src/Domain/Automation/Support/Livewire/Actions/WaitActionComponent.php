<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationActionComponent;

class WaitActionComponent extends AutomationActionComponent
{
    public int $length = 1;

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
            'length' => $this->length,
            'unit' => Str::plural($this->unit),
        ];
    }

    public function rules(): array
    {
        return [
            'length' => ['required'],
            'unit' => ['required'],
        ];
    }

    public function render()
    {
        return view('mailcoach::app.automations.components.actions.waitAction');
    }
}
