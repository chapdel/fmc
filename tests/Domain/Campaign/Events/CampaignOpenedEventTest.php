<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignOpenedEvent;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

it('fires an event when a campaign is opened', function () {
    Event::fake(CampaignOpenedEvent::class);

    /** @var Send $send */
    $send = SendFactory::new()->create();

    $send->registerOpen();

    Event::assertDispatched(CampaignOpenedEvent::class);
});
