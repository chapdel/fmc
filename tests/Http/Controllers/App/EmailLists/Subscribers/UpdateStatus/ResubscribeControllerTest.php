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

    test()->assertEquals(SubscriptionStatus::UNSUBSCRIBED, $subscriber->status);

    $this
        ->post(route('mailcoach.subscriber.resubscribe', $subscriber))
        ->assertRedirect();

    test()->assertEquals(SubscriptionStatus::SUBSCRIBED, $subscriber->refresh()->status);
});

it('will only resubscribe unsubscribed subscribers', function () {
    test()->withExceptionHandling();
    $emailList = EmailList::factory()->create();
    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);
    test()->assertEquals(SubscriptionStatus::SUBSCRIBED, $subscriber->status);

    $this
        ->post(route('mailcoach.subscriber.resubscribe', $subscriber))
        ->assertSessionHas('laravel_flash_message.class', 'error');
});
