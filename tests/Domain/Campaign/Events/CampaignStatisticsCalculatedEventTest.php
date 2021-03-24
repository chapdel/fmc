<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Events;

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignStatisticsCalculatedEvent;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Tests\TestCase;

class CampaignStatisticsCalculatedEventTest extends TestCase
{
    /** @test */
    public function it_fires_an_event_after_campaign_statistics_have_been_calculated()
    {
        Event::fake(CampaignStatisticsCalculatedEvent::class);

        $campaign = Campaign::factory()->create();

        dispatch(new CalculateStatisticsJob($campaign));

        Event::assertDispatched(CampaignStatisticsCalculatedEvent::class, function (CampaignStatisticsCalculatedEvent $event) use ($campaign) {
            $this->assertEquals($campaign->id, $event->campaign->id);

            return true;
        });
    }
}
