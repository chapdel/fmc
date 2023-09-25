<?php

use Spatie\Mailcoach\Domain\Shared\Events\WebhookCallProcessedEvent;
use Spatie\WebhookClient\Models\WebhookCall;

it('sets the processed at timestamp on the webhook call', function () {
    $webhookCall = WebhookCall::create([
        'name' => 'feedback',
    ]);

    expect($webhookCall->processed_at)->toBeNull();

    event(new WebhookCallProcessedEvent($webhookCall));

    test()->assertNotNull($webhookCall->processed_at);
});
