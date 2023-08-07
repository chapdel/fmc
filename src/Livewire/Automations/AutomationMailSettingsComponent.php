<?php

namespace Spatie\Mailcoach\Livewire\Automations;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Arr;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\MainNavigation;
use Spatie\ValidationRules\Rules\Delimited;

class AutomationMailSettingsComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public AutomationMail $mail;

    #[Rule('required')]
    public string $name;

    #[Rule(['nullable'])]
    public ?string $subject = null;

    #[Rule(['nullable', 'email:rfc'])]
    public ?string $from_email;

    #[Rule(['nullable'])]
    public ?string $from_name;

    #[Rule(['nullable', new Delimited('email:rfc')])]
    public ?string $reply_to_email;

    #[Rule(['nullable', new Delimited('string')])]
    public ?string $reply_to_name;

    #[Rule('bool')]
    public bool $utm_tags;

    #[Rule('bool')]
    public bool $add_subscriber_tags;

    #[Rule('bool')]
    public bool $add_subscriber_link_tags;

    public function mount(AutomationMail $automationMail)
    {
        $this->mail = $automationMail;
        $this->fill($automationMail->toArray());

        $this->authorize('update', $automationMail);

        app(MainNavigation::class)->activeSection()?->add($this->mail->name, route('mailcoach.automations.mails'));
    }

    public function save()
    {
        $this->validate();

        $this->mail->fill(Arr::except($this->all(), ['mail']));
        $this->mail->save();

        notify(__mc('Email :name was updated.', ['name' => $this->mail->name]));
    }

    public function render(): View
    {
        return view('mailcoach::app.automations.mails.settings')
            ->layout('mailcoach::app.automations.mails.layouts.automationMail', [
                'title' => __mc('Settings'),
                'mail' => $this->mail,
            ]);
    }
}
