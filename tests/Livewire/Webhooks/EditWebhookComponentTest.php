<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;

beforeEach(function () {
    $this->authenticate();

    $this->webhook = WebhookConfiguration::factory()->create();

    config()->set('mailcoach.webhooks.selectable_event_types_enabled', true);
});

it('can select the events to use for the webhook if the setting is enabled', function () {
    $webhook = WebhookConfiguration::factory()->create();

    Livewire::test('mailcoach::edit-webhook', ['webhook' => $this->webhook])
        ->assertSee('Use for all events');
});

it('should sync the selected events on saving', function () {
    Livewire::test('mailcoach::edit-webhook', ['webhook' => $this->webhook])
        ->set('form.events', ['SubscribedEvent', 'UnsubscribedEvent'])
        ->set('form.url', 'https://example.com/webhook')
        ->set('form.use_for_all_events', false)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertEquals(
        ['SubscribedEvent', 'UnsubscribedEvent'],
        $this->webhook->fresh()->events->toArray()
    );
});

it('should update the use_for_all_events flag', function () {
    Livewire::test('mailcoach::edit-webhook', ['webhook' => $this->webhook])
        ->set('form.events', ['SubscribedEvent', 'UnsubscribedEvent'])
        ->set('form.url', 'https://example.com/webhook')
        ->set('form.use_for_all_events', true)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertEquals($this->webhook->fresh()->use_for_all_events, true);
});
