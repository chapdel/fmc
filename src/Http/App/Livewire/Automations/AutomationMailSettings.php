<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Automations;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\MainNavigation;

class AutomationMailSettings extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;
    use LivewireFlash;

    public AutomationMail $mail;

    protected function rules(): array
    {
        return [
            'mail.name' => 'required',
            'mail.subject' => '',
            'mail.utm_tags' => 'bool',
            'mail.add_subscriber_tags' => 'bool',
            'mail.add_subscriber_link_tags' => 'bool',
        ];
    }

    public function mount(AutomationMail $automationMail)
    {
        $this->mail = $automationMail;

        $this->authorize('update', $automationMail);

        app(MainNavigation::class)->activeSection()?->add($this->mail->name, route('mailcoach.automations.mails'));
    }

    public function save()
    {
        $this->validate();

        $this->mail->save();

        $this->flash(__('mailcoach - Email :name was updated.', ['name' => $this->mail->name]));
    }

    public function render(): View
    {
        return view('mailcoach::app.automations.mails.settings')
            ->layout('mailcoach::app.automations.mails.layouts.automationMail', [
                'title' => __('mailcoach - Settings'),
                'mail' => $this->mail,
            ]);
    }
}
