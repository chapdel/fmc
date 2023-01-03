<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers;

use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationTriggerComponent;

class DateTriggerComponent extends AutomationTriggerComponent
{
    public ?array $date = null;

    public function mount()
    {
        if ($this->automation->getTrigger()->date) {
            $this->date ??= [
                'date' => $this->automation->getTrigger()->date->format('Y-m-d'),
                'hours' => (int) $this->automation->getTrigger()->date->format('H'),
                'minutes' => (int) $this->automation->getTrigger()->date->format('i'),
            ];
        }
    }

    public function render()
    {
        return view('mailcoach::app.automations.components.triggers.dateTrigger');
    }
}
