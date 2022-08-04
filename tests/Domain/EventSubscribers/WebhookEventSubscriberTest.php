<?php

use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Events\SubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\UnconfirmedSubscriberCreatedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\UnsubscribedEvent;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\WebhookServer\CallWebhookJob;

beforeEach(function () {
    Queue::fake();

    $this->subscriber = SubscriberFactory::new()->create();

    $this->webhookConfiguration = WebhookConfiguration::factory()->create();
});

it('will send a webhook when someone subscribes', function () {
    event(new SubscribedEvent($this->subscriber));

    Queue::assertPushed(CallWebhookJob::class);
});

it('will send a webhook when an unconfirmed subscriber is created', function () {
    event(new UnconfirmedSubscriberCreatedEvent($this->subscriber));

    Queue::assertPushed(CallWebhookJob::class);
});

it('will send a webhook when someone unsubscribes', function () {
    event(new UnsubscribedEvent($this->subscriber));

    Queue::assertPushed(CallWebhookJob::class);
});
