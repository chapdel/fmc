<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers;

use Carbon\CarbonInterface;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationTriggerComponent;

class DateTriggerComponent extends AutomationTriggerComponent
{
    public ?CarbonInterface $date = null;

    public function render()
    {
        return <<<'blade'
            <div>
                <x-mailcoach::date-field
                    :label="__('Date')"
                    name="date"
                    :value="$automation->trigger->date ?? null"
                    required
                />
            </div>
        blade;
    }
}
