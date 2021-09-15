<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Audience\Events\UnconfirmedSubscriberCreatedEvent;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;

it('will fire the unconfirmed subscribed created event when a subscription still needs to be confirmed', function () {
    Event::fake(UnconfirmedSubscriberCreatedEvent::class);

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */
    $emailList = EmailList::factory()->create([
        'requires_confirmation' => true,
    ]);

    $emailList->subscribe('john@example.com');

    Event::assertDispatched(UnconfirmedSubscriberCreatedEvent::class);
});
