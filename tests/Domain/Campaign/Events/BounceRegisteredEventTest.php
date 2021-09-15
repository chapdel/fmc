<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Campaign\Events\BounceRegisteredEvent;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\TestCase;



it('will send an event after a bounce has been registered', function () {
    Event::fake();

    /** @var Send $send */
    $send = SendFactory::new()->create();

    $send->registerBounce();

    Event::assertDispatched(BounceRegisteredEvent::class);
});
