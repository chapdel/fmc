<?php

namespace Spatie\Mailcoach\Http\Livewire;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookLog;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\WebhookServer\WebhookCall;

class WebhookLogComponent extends Component
{
    use LivewireFlash;
    use UsesMailcoachModels;

    public WebhookConfiguration $webhook;

    public WebhookLog $webhookLog;

    public function mount(WebhookConfiguration $webhook, WebhookLog $webhookLog)
    {
        $this->webhook = $webhook;
        $this->webhookLog = $webhookLog;
    }

    public function render()
    {
        return view('mailcoach::app.configuration.webhooks.logs.show', [
            'webhookLog' => $this->webhookLog,
        ])->layout('mailcoach::app.layouts.settings', [
            'title' => __mc('Webhook Log Details'),
        ]);
    }

    public function resend()
    {
//        $this->webhookLog->resend();
//
//        $this->flashSuccess(__mc('webhook_log.resend_success'));

        ray($this->webhookLog->payload);

        WebhookCall::create()
//            ->onQueue(config('mailcoach.shared.perform_on_queue.send_webhooks'))
            ->timeoutInSeconds(10)
            ->maximumTries(0)
            ->url($this->webhook->url)
            ->payload($this->webhookLog->payload)
            ->useSecret($this->webhook->secret)
            ->throwExceptionOnFailure()
            ->meta([
                'webhook_configuration_uuid' => $this->webhook->uuid,
            ])
            ->dispatchSync();
    }
}
