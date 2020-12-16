<?php

namespace Spatie\Mailcoach\Events;

use Spatie\WebhookClient\Models\WebhookCall;

class WebhookCallProcessedEvent
{
    public function __construct(
        public WebhookCall $webhookCall
    ) {}
}
