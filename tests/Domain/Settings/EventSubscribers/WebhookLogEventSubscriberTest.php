<?php

use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Database\Factories\WebhookConfigurationFactory;
use Spatie\Mailcoach\Domain\Settings\EventSubscribers\WebhookLogEventSubscriber;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\WebhookServer\Events\WebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;

beforeEach(function () {
    $this->subscriber = resolve(WebhookLogEventSubscriber::class);
});

function createEvent($eventClass, $callUuid, WebhookConfiguration $webhookConfiguration = null)
{
    $webhookConfiguration = $webhookConfiguration ?? WebhookConfigurationFactory::new()->create();

    return new $eventClass(
        'post',
        'https://example.com',
        [
            'tags' => [],
            'uuid' => '51e5b73f-b94d-4db3-bf5e-0796860206d5',
            'email' => 'tim+36@spatie.be',
            'event' => 'SubscribedEvent',
            'last_name' => null,
            'created_at' => '2023-02-28T10:47:31.000000Z',
            'first_name' => null,
            'updated_at' => '2023-02-28T10:47:31.000000Z',
            'subscribed_at' => '2023-02-28T10:47:31.000000Z',
            'email_list_uuid' => '3d90fd11-ca98-3719-ae82-1ffb65a59e01',
            'unsubscribed_at' => null,
            'extra_attributes' => [],
        ],
        [],
        [
            'webhook_configuration_uuid' => $webhookConfiguration->uuid,
            'webhook_call_uuid' => $callUuid,
        ],
        [],
        2,
        new Response(200, [], 'body'),
        null,
        null,
        Str::uuid(),
        null
    );
}

it('should write a WebhookCallSucceededEvent log to the database', function () {
    $callUuid = Str::uuid();
    $event = createEvent(WebhookCallSucceededEvent::class, $callUuid);

    $this->subscriber->handleWebhookEvent($event);

    test()->assertDatabaseHas('mailcoach_webhook_logs', [
        'webhook_call_uuid' => $callUuid,
        'event_type' => 'SubscribedEvent',
        'webhook_event_type' => WebhookCallSucceededEvent::class,
    ]);
});

it('should write a WebhookCallFailedEvent log to the database', function () {
    $callUuid = Str::uuid();
    $event = createEvent(WebhookCallFailedEvent::class, $callUuid);

    $this->subscriber->handleWebhookEvent($event);

    test()->assertDatabaseHas('mailcoach_webhook_logs', [
        'webhook_call_uuid' => $callUuid,
        'event_type' => 'SubscribedEvent',
        'webhook_event_type' => WebhookCallFailedEvent::class,
    ]);
});
