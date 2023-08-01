<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Database\Factories\WebhookLogFactory;

it('should render the details of a webhook log', function () {
    $webhookLog = WebhookLogFactory::new()->create();

    Livewire::test('mailcoach::webhook-log', [
        'webhook' => $webhookLog->webhookConfiguration()->first(),
        'webhookLog' => $webhookLog,
    ])->assertSee([
        $webhookLog->webhook_url,
        $webhookLog->event_type,
        $webhookLog->attempt,
    ]);
});
