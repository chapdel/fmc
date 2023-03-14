<?php

namespace Spatie\Mailcoach\Domain\Settings\EventSubscribers;

use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\WebhookServer\Events\WebhookCallEvent;
use Spatie\WebhookServer\Events\WebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;

class WebhookLogEventSubscriber
{
    use UsesMailcoachModels;

    public function subscribe(): array
    {
        if (config('mailcoach.webhooks.logs') === false) {
            return [];
        }

        return [
            WebhookCallSucceededEvent::class => 'handleWebhookEvent',
            WebhookCallFailedEvent::class => 'handleWebhookEvent',
        ];
    }

    public function handleWebhookEvent(WebhookCallEvent $event)
    {
        if (! isset($event->meta['webhook_configuration_uuid'])) {
            return;
        }

        $body = $event->response?->getBody()?->getContents();
        $decodedBody = json_decode($body);

        $webhookConfiguration = self::getWebhookConfigurationClass()::findByUuid($event->meta['webhook_configuration_uuid']);

        $data = [
            'webhook_call_uuid' => $event->meta['webhook_call_uuid'],
            'webhook_configuration_id' => $webhookConfiguration->id,
            'webhook_event_type' => get_class($event),
            'event_type' => $event->payload['event'],
            'webhook_url' => $event->webhookUrl,
            'payload' => $event->payload,
            'response' => $decodedBody ?? $body ?? '',
            'status_code' => $event->response?->getStatusCode(),
        ];

        if (! isset($event->meta['manual']) || $event->meta['manual'] !== 'true') {
            $data['attempt'] = $event->attempt;
        }

        self::getWebhookLogClass()::create($data);
    }
}
