<?php

use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    test()->authenticate();
});

it('can confirm a subscriber', function () {
    $emailList = EmailList::factory()->create();

    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);
    $subscriber->unsubscribe();

    expect($subscriber->status)->toEqual(SubscriptionStatus::UNSUBSCRIBED);

    $this
        ->post(route('mailcoach.subscriber.resubscribe', $subscriber))
        ->assertRedirect();

    expect($subscriber->refresh()->status)->toEqual(SubscriptionStatus::SUBSCRIBED);
});

it('will only resubscribe unsubscribed subscribers', function () {
    test()->withExceptionHandling();
    $emailList = EmailList::factory()->create();
    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);
    expect($subscriber->status)->toEqual(SubscriptionStatus::SUBSCRIBED);

    $this
        ->post(route('mailcoach.subscriber.resubscribe', $subscriber))
        ->assertSessionHas('laravel_flash_message.class', 'error');
});
