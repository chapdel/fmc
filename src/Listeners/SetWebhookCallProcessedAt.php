<?php

namespace Spatie\Mailcoach\Listeners;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Events\CampaignSentEvent;
use Spatie\Mailcoach\Events\WebhookCallProcessedEvent;
use Spatie\Mailcoach\Mails\CampaignSentMail;

class SetWebhookCallProcessedAt
{
    public function handle(WebhookCallProcessedEvent $event)
    {
        $event->webhookCall->update([
            'processed_at' => now(),
        ]);
    }
}
