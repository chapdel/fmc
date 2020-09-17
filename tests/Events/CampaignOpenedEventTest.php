<?php

namespace Spatie\Mailcoach\Tests\Events;

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Database\Factories\CampaignSendFactory;
use Spatie\Mailcoach\Events\CampaignOpenedEvent;
use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Tests\TestCase;

class CampaignOpenedEventTest extends TestCase
{
    /** @test */
    public function it_fires_an_event_when_a_campaign_is_opened()
    {
        Event::fake(CampaignOpenedEvent::class);

        /** @var Send $send */
        $send = CampaignSendFactory::new()->create();

        $send->campaign->update(['track_opens' => true]);

        $send->registerOpen();

        Event::assertDispatched(CampaignOpenedEvent::class);
    }

    /** @test */
    public function it_will_not_fire_an_event_when_a_campaign_is_opened_and_open_tracking_is_not_enabled()
    {
        Event::fake(CampaignOpenedEvent::class);

        /** @var Send $send */
        $send = CampaignSendFactory::new()->create();

        $send->campaign->update(['track_opens' => false]);

        $send->registerOpen();

        Event::assertNotDispatched(CampaignOpenedEvent::class);
    }
}
