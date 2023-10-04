<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Events\SoftBounceRegisteredEvent;
use Spatie\Mailcoach\Domain\Vendor\Sendinblue\Events\SoftBounceEvent;

it('can handle a soft bounce event', function () {
    Event::fake();

    $event = new SoftBounceEvent([
        'event' => 'soft_bounce',
    ]);

    expect($event->canHandlePayload())->toBeTrue();

    $send = SendFactory::new()
        ->for(Subscriber::factory()->state(['email' => 'example@spatie.be']))
        ->create();

    $event->handle($send);

    Event::assertDispatched(SoftBounceRegisteredEvent::class);
});

it('cannot handle soft bounces', function () {
    Event::fake();

    $event = new SoftBounceEvent([
        'event' => 'hard_bounce',
    ]);

    expect($event->canHandlePayload())->toBeFalse();
});
