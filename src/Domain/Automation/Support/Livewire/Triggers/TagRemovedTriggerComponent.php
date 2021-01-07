<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers;

use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationTriggerComponent;

class TagRemovedTriggerComponent extends AutomationTriggerComponent
{
    public function render()
    {
        return <<<'blade'
            <div>
                <x-mailcoach::text-field
                    :label="__('Tag')"
                    name="tag"
                    :value="$automation->trigger->tag ?? null"
                    required
                />
            </div>
        blade;
    }
}
