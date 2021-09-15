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

    test()->assertEquals(SubscriptionStatus::UNCONFIRMED, $subscriber->status);

    $this
        ->post(route('mailcoach.subscriber.confirm', $subscriber))
        ->assertRedirect();

    test()->assertEquals(SubscriptionStatus::SUBSCRIBED, $subscriber->refresh()->status);
});

it('will confirm unconfirmed subscribers', function () {
    test()->withExceptionHandling();

    $subscriber = Subscriber::factory()->create([
        'unsubscribed_at' => now(),
        'subscribed_at' => now(),
    ]);

    test()->assertEquals(SubscriptionStatus::UNSUBSCRIBED, $subscriber->status);

    $this
        ->post(route('mailcoach.subscriber.confirm', $subscriber))
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});
