<?php

use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\CreateSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Support\PendingSubscriber;
use Spatie\Mailcoach\Mailcoach;

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
