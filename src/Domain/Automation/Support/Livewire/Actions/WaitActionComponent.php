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
        return <<<'blade'
            <div class="flex gap-4">
                <x-mailcoach::text-field
                    :label="__('Length')"
                    :required="true"
                    name="length"
                    wire:model="length"
                />
                <x-mailcoach::select-field
                    :label="__('Unit')"
                    :required="true"
                    name="unit"
                    wire:model="unit"
                    :options="
                        collect($units)
                            ->mapWithKeys(fn ($label, $value) => [$value => \Illuminate\Support\Str::plural($label, $length)])
                            ->toArray()
                    "
                />
            </div>
        blade;
    }
}
