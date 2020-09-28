<?php

namespace Spatie\Mailcoach\Listeners;

use Spatie\Mailcoach\Events\WebhookCallProcessedEvent;

class SetWebhookCallProcessedAt
{
    public function handle(WebhookCallProcessedEvent $event)
    {
        $event->webhookCall->update([
            'processed_at' => now(),
        ]);
    }
}
