<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\CreateSubscriberController;

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

    expect($subscriber->email)->toEqual('john@example.com');
    expect($subscriber->first_name)->toEqual('John');
    expect($subscriber->last_name)->toEqual('Doe');

    expect($emailList->isSubscribed($subscriber->email))->toBeTrue();
});
