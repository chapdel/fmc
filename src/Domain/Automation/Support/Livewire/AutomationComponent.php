<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire;

use Illuminate\Support\MessageBag;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

abstract class AutomationComponent extends Component
{
    use UsesMailcoachModels;

    public Automation $automation;

    public array $componentData = [];

    public function mount()
    {
        foreach ($this->componentData as $key => $value) {
            $this->$key = $value;
        }
    }

    abstract public function render();
}
