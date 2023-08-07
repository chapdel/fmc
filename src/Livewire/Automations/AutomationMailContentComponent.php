<?php

namespace Spatie\Mailcoach\Livewire\Automations;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\MainNavigation;

class AutomationMailContentComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public AutomationMail $mail;

    #[Rule(['nullable', 'string'])]
    public ?string $subject;

    public array $templateOptions;

    protected $listeners = [
        'editorSaved' => 'save',
        'editorSavedQuietly' => 'save',
    ];

    public function mount(AutomationMail $automationMail)
    {
        $this->mail = $automationMail;
        $this->subject = $automationMail->subject;

        $this->authorize('update', $automationMail);

        $this->templateOptions = self::getTemplateClass()::all()
            ->pluck('name', 'id')
            ->toArray();

        app(MainNavigation::class)->activeSection()?->add($this->mail->name, route('mailcoach.automations.mails'));
    }

    public function save()
    {
        $this->validate();

        $this->mail->subject = $this->subject;
        $this->mail->save();
    }

    public function render(): View
    {
        return view('mailcoach::app.automations.mails.content')
            ->layout('mailcoach::app.automations.mails.layouts.automationMail', [
                'title' => __mc('Content'),
                'mail' => $this->mail,
            ]);
    }
}
