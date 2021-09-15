<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\CreateSubscriberController;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

it('can create a subscriber', function () {
    test()->authenticate();

    /** @var EmailList $emailList */
    $emailList = EmailList::factory()->create();

    $attributes = [
        'email' => 'john@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
    ];

    $this
        ->post(action([CreateSubscriberController::class, 'store'], $emailList), $attributes)
        ->assertSessionHasNoErrors();

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
    $subscriber = Subscriber::first();

    test()->assertEquals('john@example.com', $subscriber->email);
    test()->assertEquals('John', $subscriber->first_name);
    test()->assertEquals('Doe', $subscriber->last_name);

    test()->assertTrue($emailList->isSubscribed($subscriber->email));
});
