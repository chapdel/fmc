<?php

use Illuminate\Http\Response;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers\ResubscribeController;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->withExceptionHandling();

    test()->loginToApi();
});

it('can resubscribe a subscriber via the api', function () {
    $subscriber = SubscriberFactory::new()->unsubscribed()->create();

    $this
        ->postJson(action(ResubscribeController::class, $subscriber))
        ->assertSuccessful();
    expect($subscriber->refresh()->status)->toEqual(SubscriptionStatus::Subscribed);
});

it('cannot resubscribe someone that is already subscribed', function () {
    $subscriber = SubscriberFactory::new()->create();

    $this
        ->postJson(action(ResubscribeController::class, $subscriber))
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});
