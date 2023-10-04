<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Shared\Events\SoftBounceRegisteredEvent;
use Spatie\Mailcoach\Domain\Vendor\Ses\Events\SoftBounce;

it('can handle a soft bounce event', function () {
    Event::fake();

    $event = new SoftBounce([
        'eventType' => 'Bounce',
        'bounce' => [
            'bounceType' => 'Undetermined',
            'timestamp' => 1610000000,
        ],
    ]);

    expect($event->canHandlePayload())->toBeTrue();

    $event->handle(SendFactory::new()->create());

    Event::assertDispatched(SoftBounceRegisteredEvent::class);
});

it('cannot handle soft bounces', function (array $payload) {
    $event = new SoftBounce($payload);

    expect($event->canHandlePayload())->toBeFalse();
})->with('failures');

dataset('failures', function () {
    yield 'different event' => [
        [
            'eventType' => 'SomethingElse',
            'bounce' => ['bounceType' => 'Undetermined'],
        ],
    ];

    yield 'different type' => [
        [
            'eventType' => 'Bounce',
            'bounce' => ['bounceType' => 'Permanent'],
        ],
    ];
});
