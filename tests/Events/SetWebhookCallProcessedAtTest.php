<?php

namespace Spatie\Mailcoach\Tests\Events;

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Campaign\Events\WebhookCallProcessedEvent;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\WebhookClient\Models\WebhookCall;

class SetWebhookCallProcessedAtTest extends TestCase
{
    /** @test */
    public function it_sets_the_processed_at_timestamp_on_the_webhook_call()
    {
        $webhookCall = WebhookCall::create([
            'name' => 'feedback',
        ]);

        $this->assertNull($webhookCall->processed_at);

        event(new WebhookCallProcessedEvent($webhookCall));

        $this->assertNotNull($webhookCall->processed_at);
    }
}
