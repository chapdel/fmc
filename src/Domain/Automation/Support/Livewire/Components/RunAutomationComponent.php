<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Components;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class RunAutomationComponent extends Component
{
    use UsesMailcoachModels;

    public Automation $automation;

    public string $interval;

    public function pause(): void
    {
        $this->automation->pause();
    }

    public function start(): void
    {
        $this->automation->start();
    }

    public function mount()
    {
        $this->interval = $this->automation->interval;
    }

    public function saveInterval()
    {
        $this->automation->update(['interval' => $this->interval]);
    }

    public function render()
    {
        return view('mailcoach::app.automations.components.runForm');
    }
}
