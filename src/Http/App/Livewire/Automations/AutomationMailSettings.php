<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Automations;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;

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
            'mail.track_opens' => 'bool',
            'mail.track_clicks' => 'bool',
            'mail.utm_tags' => 'bool',
        ];
    }

    public function mount(AutomationMail $automationMail)
    {
        $this->mail = $automationMail;

        $this->authorize('update', $automationMail);
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
