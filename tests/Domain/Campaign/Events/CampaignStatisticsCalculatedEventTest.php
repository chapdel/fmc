<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignStatisticsCalculatedEvent;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

it('fires an event after campaign statistics have been calculated', function () {
    Event::fake(CampaignStatisticsCalculatedEvent::class);

    $campaign = Campaign::factory()->create();

    Send::factory()->create(['content_item_id' => $campaign->contentItem->id]);

    dispatch(new CalculateStatisticsJob($campaign->contentItem));

    Event::assertDispatched(CampaignStatisticsCalculatedEvent::class, function (CampaignStatisticsCalculatedEvent $event) use ($campaign) {
        expect($event->campaign->id)->toEqual($campaign->id);

        return true;
    });
});
