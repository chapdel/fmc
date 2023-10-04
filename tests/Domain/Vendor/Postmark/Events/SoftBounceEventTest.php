<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Shared\Events\SoftBounceRegisteredEvent;
use Spatie\Mailcoach\Domain\Vendor\Postmark\Events\SoftBounceEvent;

it('can handle a soft bounce event', function () {
    Event::fake();

    $event = new SoftBounceEvent([
        'RecordType' => 'Bounce',
        'Type' => 'DnsError',
        'BouncedAt' => 1610000000,
    ]);

    expect($event->canHandlePayload())->toBeTrue();

    $event->handle(SendFactory::new()->create());

    Event::assertDispatched(SoftBounceRegisteredEvent::class);
});

it('cannot handle soft bounces', function (array $payload) {
    Event::fake();

    $event = new SoftBounceEvent($payload);

    expect($event->canHandlePayload())->toBeFalse();
})->with('failures');

dataset('failures', function () {
    yield 'different event' => [
        [
            'RecordType' => 'Something else',
            'Type' => 'DnsError',
        ],
    ];

    yield 'different type' => [
        [
            'RecordType' => 'Bounce',
            'Type' => 'something else',
        ],
    ];
});
