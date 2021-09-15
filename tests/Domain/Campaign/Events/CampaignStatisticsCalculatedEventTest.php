<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignStatisticsCalculatedEvent;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

it('fires an event after campaign statistics have been calculated', function () {
    Event::fake(CampaignStatisticsCalculatedEvent::class);

    $campaign = Campaign::factory()->create();

    dispatch(new CalculateStatisticsJob($campaign));

    Event::assertDispatched(CampaignStatisticsCalculatedEvent::class, function (CampaignStatisticsCalculatedEvent $event) use ($campaign) {
        test()->assertEquals($campaign->id, $event->campaign->id);

        return true;
    });
});
