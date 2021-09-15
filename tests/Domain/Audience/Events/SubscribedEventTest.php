<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Audience\Events\SubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Tests\TestCase;



beforeEach(function () {
    Event::fake(SubscribedEvent::class);
});

it('fires an event when someone subscribes', function () {
    /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */
    $emailList = EmailList::factory()->create([
        'requires_confirmation' => false,
    ]);

    $emailList->subscribe('john@example.com');

    Event::assertDispatched(SubscribedEvent::class, function (SubscribedEvent $event) {
        expect($event->subscriber->email)->toEqual('john@example.com');

        return true;
    });
});

it('will not fire the subscription event when a subscription still needs to be confirmed', function () {
    /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */
    $emailList = EmailList::factory()->create([
        'requires_confirmation' => true,
    ]);

    $emailList->subscribe('john@example.com');

    Event::assertNotDispatched(SubscribedEvent::class);
});

it('will fire the subscribe event when a subscription is confirmed', function () {
    /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */
    $emailList = EmailList::factory()->create([
        'requires_confirmation' => true,
    ]);

    $subcriber = $emailList->subscribe('john@example.com');

    Event::assertNotDispatched(SubscribedEvent::class);

    $subcriber->confirm();

    Event::assertDispatched(SubscribedEvent::class, function (SubscribedEvent $event) {
        expect($event->subscriber->email)->toEqual('john@example.com');

        return true;
    });
});

it('passes custom attributes correctly', function () {
    /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */
    $emailList = EmailList::factory()->create([
        'requires_confirmation' => true,
    ]);

    $subcriber = $emailList->subscribe('john@example.com', [
        'extra_attributes' => [
            'foo' => 'bar',
        ],
    ]);

    Event::assertNotDispatched(SubscribedEvent::class);

    $subcriber->confirm();

    Event::assertDispatched(SubscribedEvent::class, function (SubscribedEvent $event) {
        expect($event->subscriber->email)->toEqual('john@example.com');
        expect($event->subscriber->extra_attributes->foo)->toEqual('bar');

        return true;
    });
});
