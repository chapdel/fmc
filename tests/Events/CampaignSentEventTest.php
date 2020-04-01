<?php

namespace Spatie\Mailcoach\Tests\Events;

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Events\CampaignSentEvent;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\TestCase;

class CampaignSentEventTest extends TestCase
{
    /** @test */
    public function it_fires_an_event_after_a_campaign_has_been_sent()
    {
        Event::fake(CampaignSentEvent::class);

        $campaign = (new CampaignFactory())->withSubscriberCount(3)->create();

        $campaign->send();

        Event::assertDispatched(CampaignSentEvent::class, function (CampaignSentEvent $event) use ($campaign) {
            $this->assertEquals($campaign->id, $event->campaign->id);

            return true;
        });
    }
}
