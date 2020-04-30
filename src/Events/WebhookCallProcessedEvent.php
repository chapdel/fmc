<?php

namespace Spatie\Mailcoach\Events;

use Spatie\WebhookClient\Models\WebhookCall;

class WebhookCallProcessedEvent
{
    public WebhookCall $webhookCall;

    public function __construct(WebhookCall $webhookCall)
    {
        $this->webhookCall = $webhookCall;
    }
}
