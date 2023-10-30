<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Content\Events\ContentOpenedEvent;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

it('fires an event when a campaign is opened', function () {
    Event::fake(ContentOpenedEvent::class);

    /** @var Send $send */
    $send = SendFactory::new()->create();

    $send->registerOpen();

    Event::assertDispatched(ContentOpenedEvent::class);
});
