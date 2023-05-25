<?php

use Illuminate\Support\Str;
use Spatie\Mailcoach\Database\Factories\WebhookConfigurationFactory;
use Spatie\Mailcoach\Domain\Settings\EventSubscribers\WebhookFailedAttemptsSubscriber;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\WebhookServer\Events\FinalWebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;

beforeEach(function () {
    config()->set('mailcoach.opt_in_features.disable_failed_webhooks', true);
    $this->subscriber = resolve(WebhookFailedAttemptsSubscriber::class);
});

it('disables a webhook after the maximum failed attempts has been reached', function () {
    config()->set('mailcoach.webhooks.maximum_attempts', 3);
    $webhookConfiguration = WebhookConfigurationFactory::new()->create();

    test()->assertDatabaseHas(WebhookConfiguration::class, [
        'id' => $webhookConfiguration->id,
        'enabled' => true,
        'failed_attempts' => 0,
    ]);

    $event = createEvent(FinalWebhookCallFailedEvent::class, Str::uuid(), $webhookConfiguration);
    $this->subscriber->handleWebhookEvent($event);

    test()->assertDatabaseHas(WebhookConfiguration::class, [
        'id' => $webhookConfiguration->id,
        'enabled' => true,
        'failed_attempts' => 1,
    ]);

    $event = createEvent(FinalWebhookCallFailedEvent::class, Str::uuid(), $webhookConfiguration);
    $this->subscriber->handleWebhookEvent($event);

    test()->assertDatabaseHas(WebhookConfiguration::class, [
        'id' => $webhookConfiguration->id,
        'enabled' => true,
        'failed_attempts' => 2,
    ]);

    $event = createEvent(FinalWebhookCallFailedEvent::class, Str::uuid(), $webhookConfiguration);
    $this->subscriber->handleWebhookEvent($event);

    test()->assertDatabaseHas(WebhookConfiguration::class, [
        'id' => $webhookConfiguration->id,
        'enabled' => false,
        'failed_attempts' => 3,
    ]);
});

it('resets the failed attempts on a successful webhook', function () {
    config()->set('mailcoach.webhooks.maximum_attempts', 3);
    $webhookConfiguration = WebhookConfigurationFactory::new()->create();

    test()->assertDatabaseHas(WebhookConfiguration::class, [
        'id' => $webhookConfiguration->id,
        'enabled' => true,
        'failed_attempts' => 0,
    ]);

    $event = createEvent(FinalWebhookCallFailedEvent::class, Str::uuid(), $webhookConfiguration);
    $this->subscriber->handleWebhookEvent($event);

    test()->assertDatabaseHas(WebhookConfiguration::class, [
        'id' => $webhookConfiguration->id,
        'enabled' => true,
        'failed_attempts' => 1,
    ]);

    $event = createEvent(WebhookCallSucceededEvent::class, Str::uuid(), $webhookConfiguration);
    $this->subscriber->handleWebhookEvent($event);

    test()->assertDatabaseHas(WebhookConfiguration::class, [
        'id' => $webhookConfiguration->id,
        'enabled' => true,
        'failed_attempts' => 0,
    ]);
});

it('should not disables if feature flag is disabled', function () {
    config()->set('mailcoach.opt_in_features.disable_failed_webhooks', false);
    config()->set('mailcoach.webhooks.maximum_attempts', 3);

    $webhookConfiguration = WebhookConfigurationFactory::new()->create();

    test()->assertDatabaseHas(WebhookConfiguration::class, [
        'id' => $webhookConfiguration->id,
        'enabled' => true,
        'failed_attempts' => 0,
    ]);

    $event = createEvent(FinalWebhookCallFailedEvent::class, Str::uuid(), $webhookConfiguration);
    $this->subscriber->handleWebhookEvent($event);

    test()->assertDatabaseHas(WebhookConfiguration::class, [
        'id' => $webhookConfiguration->id,
        'enabled' => true,
        'failed_attempts' => 0,
    ]);
});
