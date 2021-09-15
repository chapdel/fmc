<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Audience\Events\UnsubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;

it('will fire an event when someone unsubscribes', function () {
    Event::fake(UnsubscribedEvent::class);

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscription */
    $subscriber = Subscriber::factory()->create();

    $subscriber->unsubscribe();

    Event::assertDispatched(UnsubscribedEvent::class, function (UnsubscribedEvent $event) use ($subscriber) {
        expect($event->subscriber->id)->toEqual($subscriber->id);

        return true;
    });
});
