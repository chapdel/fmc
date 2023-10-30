<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Shared\Events\SoftBounceRegisteredEvent;
use Spatie\Mailcoach\Domain\Vendor\Mailgun\Events\SoftBounceEvent;

it('can handle a soft bounce event', function () {
    Event::fake();

    $event = new SoftBounceEvent([
        'event-data' => [
            'event' => 'failed',
            'severity' => 'temporary',
            'timestamp' => 1610000000,
        ],
        'email' => 'example@spatie.be',
    ]);

    expect($event->canHandlePayload())->toBeTrue();

    $event->handle(SendFactory::new()->create());

    Event::assertDispatched(SoftBounceRegisteredEvent::class);
});

it('cannot handle soft bounces', function (array $payload) {
    $event = new SoftBounceEvent($payload);

    expect($event->canHandlePayload())->toBeFalse();
})->with('failures');

dataset('failures', function () {
    yield 'different event' => [
        [
            'event-data' => [
                'event' => 'something-else',
                'severity' => 'temporary',
            ],
        ],
    ];

    yield 'different type' => [
        [
            'event-data' => [
                'event' => 'failed',
                'severity' => 'permanent',
            ],
        ],
    ];
});
