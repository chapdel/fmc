<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignOpenedEvent;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\TestCase;



it('fires an event when a campaign is opened', function () {
    Event::fake(CampaignOpenedEvent::class);

    /** @var Send $send */
    $send = SendFactory::new()->create();

    $send->campaign->update(['track_opens' => true]);

    $send->registerOpen();

    Event::assertDispatched(CampaignOpenedEvent::class);
});

it('will not fire an event when a campaign is opened and open tracking is not enabled', function () {
    Event::fake(CampaignOpenedEvent::class);

    /** @var Send $send */
    $send = SendFactory::new()->create();

    $send->campaign->update(['track_opens' => false]);

    $send->registerOpen();

    Event::assertNotDispatched(CampaignOpenedEvent::class);
});
