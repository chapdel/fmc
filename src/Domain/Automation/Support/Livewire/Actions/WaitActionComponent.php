<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions;

use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationComponent;

class WaitActionComponent extends AutomationComponent
{
    public string $duration = '';

    public function getData(): array
    {
        return [
            'duration' => $this->duration,
        ];
    }

    public function rules(): array
    {
        return [
            'duration' => ['required'],
        ];
    }

    public function render()
    {
        return <<<'blade'
            <div>
                <x-mailcoach::text-field
                    :label="__('Duration')"
                    :required="true"
                    name="duration"
                    wire:model="duration"
                />
            </div>
        blade;
    }
}
