<?php

use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;

beforeEach(function () {
    test()->authenticate();
});

it('can unsubscribe a subscriber', function () {
    $emailList = EmailList::factory()->create();

    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);

    expect($subscriber->status)->toEqual(SubscriptionStatus::SUBSCRIBED);

    $this
        ->post(route('mailcoach.subscriber.unsubscribe', $subscriber))
        ->assertRedirect();

    expect($subscriber->refresh()->status)->toEqual(SubscriptionStatus::UNSUBSCRIBED);
});

it('will only unsubscribe subscribed subscribers', function () {
    test()->withExceptionHandling();

    $emailList = EmailList::factory()->create();
    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);
    $subscriber->unsubscribe();

    expect($subscriber->status)->toEqual(SubscriptionStatus::UNSUBSCRIBED);

    $this
        ->post(route('mailcoach.subscriber.unsubscribe', $subscriber))
        ->assertSessionHas('laravel_flash_message.class', 'error');
});
