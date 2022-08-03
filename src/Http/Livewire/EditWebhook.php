<?php

namespace Spatie\Mailcoach\Http\Livewire;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;

class EditWebhook extends Component
{
    use LivewireFlash;

    public WebhookConfiguration $webhook;

    public function rules(): array
    {
        return [
            'webhook.name' => ['required'],
            'webhook.url' => ['required', 'url', 'starts_with:https'],
            'webhook.signature_header_name' => ['required'],
            'webhook.secret' => ['required'],
            'webhook.use_for_all_lists' => ['boolean'],
        ];
    }

    public function mount(WebhookConfiguration $webhook)
    {
        $this->webhook = $webhook;
    }

    public function save()
    {
        $this->webhook->update($this->validate()['webhook']);

        $this->flash(__('The webhook has been updated.'));
    }

    public function render()
    {
        return view("mailcoach::app.configuration.webhooks.edit")
            ->layout('mailcoach::app.layouts.settings', ['title' => $this->webhook->name]);
    }
}
