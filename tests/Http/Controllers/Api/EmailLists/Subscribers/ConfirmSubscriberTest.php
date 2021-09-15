<?php

use Illuminate\Http\Response;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers\ConfirmSubscriberController;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);
uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->withExceptionHandling();

    test()->loginToApi();
});

it('can confirm a subscriber using the api', function () {
    $subscriber = SubscriberFactory::new()->unconfirmed()->create();

    $this
        ->postJson(action(ConfirmSubscriberController::class, $subscriber))
        ->assertSuccessful();

    test()->assertEquals(SubscriptionStatus::SUBSCRIBED, $subscriber->refresh()->status);
});

it('will not confirm a subscriber that was already confirmed', function () {
    $subscriber = SubscriberFactory::new()->confirmed()->create();

    $this
        ->postJson(action(ConfirmSubscriberController::class, $subscriber))
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});
