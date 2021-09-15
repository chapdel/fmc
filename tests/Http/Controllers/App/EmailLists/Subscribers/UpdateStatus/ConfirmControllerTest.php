<?php

use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

uses(TestCase::class);

beforeEach(function () {
    test()->authenticate();
});

it('can confirm a subscriber', function () {
    $emailList = EmailList::factory()->create(['requires_confirmation' => true]);

    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);

    expect($subscriber->status)->toEqual(SubscriptionStatus::UNCONFIRMED);

    $this
        ->post(route('mailcoach.subscriber.confirm', $subscriber))
        ->assertRedirect();

    expect($subscriber->refresh()->status)->toEqual(SubscriptionStatus::SUBSCRIBED);
});

it('will confirm unconfirmed subscribers', function () {
    test()->withExceptionHandling();

    $subscriber = Subscriber::factory()->create([
        'unsubscribed_at' => now(),
        'subscribed_at' => now(),
    ]);

    expect($subscriber->status)->toEqual(SubscriptionStatus::UNSUBSCRIBED);

    $this
        ->post(route('mailcoach.subscriber.confirm', $subscriber))
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});
