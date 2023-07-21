<?php

namespace Spatie\Mailcoach\Livewire\Automations;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Livewire\LivewireFlash;
use Spatie\Mailcoach\MainNavigation;

class RunAutomationComponent extends Component
{
    use AuthorizesRequests;
    use LivewireFlash;
    use UsesMailcoachModels;

    public Automation $automation;

    public string $interval;

    public string $error;

    protected function rules(): array
    {
        return [
            'interval' => ['required'],
        ];
    }

    public function mount(Automation $automation)
    {
        $this->authorize('update', $automation);

        $this->automation = $automation;
        $this->interval = $automation->interval ?? '10 minutes';

        app(MainNavigation::class)->activeSection()?->add($automation->name, route('mailcoach.automations'));
    }

    public function pause(): void
    {
        $this->automation->pause();
        $this->dispatch('automation-paused');
    }

    public function start(): void
    {
        try {
            $this->automation->start();
            $this->dispatch('automation-started');
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }
    }

    public function save()
    {
        $this->validate();

        $this->automation->interval = $this->interval;
        $this->automation->save();

        $this->flash(__mc('Automation :automation was updated.', ['automation' => $this->automation->name]));
    }

    public function render(): View
    {
        return view('mailcoach::app.automations.run')
            ->layout('mailcoach::app.automations.layouts.automation', [
                'automation' => $this->automation,
                'title' => __mc('Run'),
            ]);
    }
}
