<?php

use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfigurationEvent;

beforeEach(function () {
    $this->authenticate();

    $this->webhook = WebhookConfiguration::factory()->create();

    config()->set('mailcoach.webhooks.selectable_event_types_enabled', true);
});

it('should not allow selecting events if the config setting is disabled', function () {
    config()->set('mailcoach.webhooks.selectable_event_types_enabled', false);

    \Livewire\Livewire::test('mailcoach::edit-webhook', ['webhook' => $this->webhook])
        ->assertDontSee('Use for all events');
});

it('can select the events to use for the webhook if the setting is enabled', function () {
    $webhook = WebhookConfiguration::factory()->create();

    \Livewire\Livewire::test('mailcoach::edit-webhook', ['webhook' => $this->webhook])
        ->assertSee('Use for all events');
});

it('should sync the selected events on saving', function () {
    WebhookConfigurationEvent::create([
        'webhook_configuration_id' => $this->webhook->id,
        'name' => 'TagRemovedEvent',
    ]);

    \Livewire\Livewire::test('mailcoach::edit-webhook', ['webhook' => $this->webhook])
        ->set('selected_events', ['SubscribedEvent', 'UnsubscribedEvent'])
        ->set('webhook.url', 'https://example.com/webhook')
        ->set('webhook.use_for_all_events', false)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertEquals(
        ['SubscribedEvent', 'UnsubscribedEvent'],
        $this->webhook->fresh()->events->pluck('name')->toArray()
    );
});

it('should update the use_for_all_events flag', function () {
    \Livewire\Livewire::test('mailcoach::edit-webhook', ['webhook' => $this->webhook])
        ->set('selected_events', ['SubscribedEvent', 'UnsubscribedEvent'])
        ->set('webhook.url', 'https://example.com/webhook')
        ->set('webhook.use_for_all_events', true)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertEquals($this->webhook->fresh()->use_for_all_events, true);
});
