<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Components;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class RunAutomationComponent extends Component
{
    use UsesMailcoachModels;

    public Automation $automation;

    public function pause(): void
    {
        $this->automation->pause();
    }

    public function start(): void
    {
        $this->automation->start();
    }

    public function render()
    {
        return view('mailcoach::app.automations.partials.runForm');
    }
}
