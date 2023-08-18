<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions;

use Carbon\CarbonInterval;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationActionComponent;
use Throwable;

class WaitActionComponent extends AutomationActionComponent
{
    public ?string $length = '1';

    public ?string $unit = 'days';

    public array $units = [
        'minutes' => 'Minute',
        'hours' => 'Hour',
        'days' => 'Day',
        'weeks' => 'Week',
        'months' => 'Month',
    ];

    public function mount()
    {
        $this->length ??= '1';
        $this->unit ??= 'days';
    }

    public function getData(): array
    {
        $unit = $this->unit;
        $interval = CarbonInterval::$unit($this->length);

        return [
            'seconds' => $interval->totalSeconds,
            'unit' => $this->unit,
            'length' => $this->length,
        ];
    }

    public function getDescriptionProperty(): string
    {
        if (! $this->length || ! $this->unit) {
            return '…';
        }

        if ($this->length <= 30) {
            return $this->length.' '.Str::plural(Str::singular($this->unit), $this->length);
        }

        try {
            $interval = CarbonInterval::{$this->unit}($this->length);

            return $interval?->cascade()->forHumans() ?? '…';
        } catch (Throwable) {
            return '…';
        }
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
