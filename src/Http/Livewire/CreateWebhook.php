<?php

namespace Spatie\Mailcoach\Http\Livewire;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Illuminate\Support\Str;

class CreateWebhook extends Component
{
    use UsesMailcoachModels;

    public string $name = '';
    public string $url = '';

    public function saveWebhook()
    {
        $this->validate([
            'name' => ['required', 'string'],
            'url' => ['required', 'string', 'url', 'starts_with:https://'],
        ]);

        $mailer = self::getWebhookConfigurationClass()::create([
            'name' => $this->name,
            'url' => $this->url,
            'signature_header_name' => 'mailcoach-secret',
            'secret' => Str::random(),
        ]);

        flash()->success(__('The webhook has been created.'));

        return redirect()->route('webhooks.edit', $mailer);
    }

    public function render()
    {
        return view('mailcoach::app.configuration.webhooks.partials.create');
    }
}
