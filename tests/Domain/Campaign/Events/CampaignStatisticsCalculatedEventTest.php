<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignStatisticsCalculatedEvent;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Jobs\CalculateStatisticsJob;

it('fires an event after campaign statistics have been calculated', function () {
    Event::fake(CampaignStatisticsCalculatedEvent::class);

    $campaign = Campaign::factory()->create();

    \Spatie\Mailcoach\Domain\Shared\Models\Send::factory()->create(['campaign_id' => $campaign->id]);

    dispatch(new CalculateStatisticsJob($campaign));

    Event::assertDispatched(CampaignStatisticsCalculatedEvent::class, function (CampaignStatisticsCalculatedEvent $event) use ($campaign) {
        expect($event->campaign->id)->toEqual($campaign->id);

        return true;
    });
});
