<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers;

use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationTriggerComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\DateTrigger;

class DateTriggerComponent extends AutomationTriggerComponent
{
    public ?array $date = null;

    public function mount()
    {
        if (! $trigger = $this->automation->getTrigger()) {
            return;
        }

        if (! $trigger instanceof DateTrigger) {
            return;
        }

        $this->date ??= [
            'date' => $trigger->date->format('Y-m-d'),
            'hours' => (int) $trigger->date->format('H'),
            'minutes' => (int) $trigger->date->format('i'),
        ];
    }

    public function render()
    {
        return view('mailcoach::app.automations.components.triggers.dateTrigger');
    }
}
