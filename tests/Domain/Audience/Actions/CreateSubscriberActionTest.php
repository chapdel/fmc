<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Events\SubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Support\PendingSubscriber;

beforeEach(function () {
    test()->subscriber = Subscriber::factory()->create();

    test()->emailList = EmailList::factory()->create();

    test()->anotherEmailList = EmailList::factory()->create();

    test()->newAttributes = [
        'email' => 'john@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
    ];
});

it('does not override the subscribed_at', function () {
    \Spatie\TestTime\TestTime::freeze();

    $existingSubscriber = Subscriber::factory()->create([
        'email' => 'john@doe.com',
        'subscribed_at' => now()->subDays(1),
    ]);

    (new PendingSubscriber('john@doe.com'))->subscribeTo($existingSubscriber->emailList);

    $existingSubscriber->refresh();

    expect($existingSubscriber->subscribed_at->startOfSecond())->toEqual(now()->subDays(1)->startOfSecond());
});

it('does not dispatch an event when importing', function () {
    Event::fake(SubscribedEvent::class);

    $emailList = EmailList::factory()->create();

    self::getSubscriberClass()::createWithEmail('john@doe.com', [
        'imported_via_import_uuid' => Str::uuid()->toString(),
    ])
        ->skipConfirmation()
        ->tags([])
        ->replaceTags(true)
        ->subscribeTo($emailList);

    Event::assertNotDispatched(SubscribedEvent::class);
});
