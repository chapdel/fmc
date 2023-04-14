<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Campaign\Events\BounceRegisteredEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\SoftBounceRegisteredEvent;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

it('will send an event after a bounce has been registered', function () {
    Event::fake(BounceRegisteredEvent::class);

    /** @var Send $send */
    $send = SendFactory::new()->create();

    $send->registerBounce();

    Event::assertDispatched(BounceRegisteredEvent::class);
});

it('will send an event after a soft bounce has been registered', function () {
    Event::fake(SoftBounceRegisteredEvent::class);

    /** @var Send $send */
    $send = SendFactory::new()->create();

    $send->registerBounce(now(), softBounce: true);

    Event::assertDispatched(SoftBounceRegisteredEvent::class);
});
