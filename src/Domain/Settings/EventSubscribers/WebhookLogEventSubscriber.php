<?php

namespace Spatie\Mailcoach\Domain\Settings\EventSubscribers;

use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookLog;
use Spatie\WebhookServer\Events\FinalWebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallEvent;
use Spatie\WebhookServer\Events\WebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;

class WebhookLogEventSubscriber
{
    public function subscribe(): array
    {
        return [
            WebhookCallSucceededEvent::class => 'handleWebhookEvent',
            WebhookCallFailedEvent::class => 'handleWebhookEvent',
            FinalWebhookCallFailedEvent::class => 'handleWebhookEvent',
        ];
    }

    public function handleWebhookEvent(WebhookCallEvent $event)
    {
        $body = $event->response?->getBody()?->getContents();

        $webhookConfiguration = WebhookConfiguration::findByUuid($event->meta['webhook_configuration_uuid']);

        WebhookLog::create([
            'webhook_call_uuid' => $event->uuid,
            'webhook_configuration_id' => $webhookConfiguration->id,
            'webhook_event_type' => get_class($event),
            'event_type' => $event->payload['event'],
            'attempt' => $event->attempt,
            'webhook_url' => $event->webhookUrl,
            'payload' => json_encode($event->payload),
            'response' => json_encode($body),
            'status_code' => $event->response?->getStatusCode(),
        ]);
    }
}
