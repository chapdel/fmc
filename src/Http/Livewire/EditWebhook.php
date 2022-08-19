<?php

namespace Spatie\Mailcoach\Http\Livewire;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;

class EditWebhook extends Component
{
    use LivewireFlash;

    public WebhookConfiguration $webhook;

    public array $email_lists;

    public function rules(): array
    {
        return [
            'webhook.name' => ['required'],
            'webhook.url' => ['required', 'url', 'starts_with:https'],
            'webhook.secret' => ['required'],
            'webhook.use_for_all_lists' => ['boolean'],
            'email_lists' => [],
        ];
    }

    public function mount(WebhookConfiguration $webhook)
    {
        $this->webhook = $webhook;

        $this->email_lists = $webhook->emailLists->pluck('id')->values()->toArray();
    }

    public function save()
    {
        $this->webhook->update($this->validate()['webhook']);
        $this->webhook->emailLists()->sync($this->email_lists);

        $this->flash(__('The webhook has been updated.'));
    }

    public function render()
    {
        return view('mailcoach::app.configuration.webhooks.edit')
            ->layout('mailcoach::app.layouts.settings', ['title' => $this->webhook->name]);
    }
}
