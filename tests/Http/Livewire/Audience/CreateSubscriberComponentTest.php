<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Http\App\Livewire\Audience\CreateSubscriberComponent;

it('can create a subscriber', function () {
    test()->authenticate();

    /** @var EmailList $emailList */
    $emailList = EmailList::factory()->create();

    Livewire::test(CreateSubscriberComponent::class, ['emailList' => $emailList])
        ->set('email', 'john@example.com')
        ->set('first_name', 'John')
        ->set('last_name', 'Doe')
        ->call('saveSubscriber');

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
    $subscriber = Subscriber::first();

    expect($subscriber->email)->toEqual('john@example.com');
    expect($subscriber->first_name)->toEqual('John');
    expect($subscriber->last_name)->toEqual('Doe');

    expect($emailList->isSubscribed($subscriber->email))->toBeTrue();
});
