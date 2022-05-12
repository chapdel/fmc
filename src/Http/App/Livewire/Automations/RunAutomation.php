<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Automations;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\EverySubscriberSegment;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\SubscribersWithTagsSegment;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;

class RunAutomation extends Component
{
    use AuthorizesRequests;
    use LivewireFlash;
    use UsesMailcoachModels;

    public Automation $automation;

    public string $error;

    protected function rules(): array
    {
        return [
            'automation.interval' => ['required'],
        ];
    }

    public function mount(Automation $automation)
    {
        $this->automation = $automation;

        $this->authorize('update', $this->automation);
    }

    public function pause(): void
    {
        $this->automation->pause();
    }

    public function start(): void
    {
        try {
            $this->automation->start();
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }
    }

    public function save()
    {
        $this->validate();

        $this->automation->save();

        $this->flash(__('mailcoach - Automation :automation was updated.', ['automation' => $this->automation->name]));
    }

    public function render(): View
    {
        return view('mailcoach::app.automations.run')
            ->layout('mailcoach::app.automations.layouts.automation', [
                'automation' => $this->automation,
            ]);
    }
}
