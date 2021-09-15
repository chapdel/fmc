<?php

use Illuminate\Http\Response;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers\UnsubscribeController;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->withExceptionHandling();

    test()->loginToApi();
});

it('can unsubscribe a subscriber via the api', function () {
    $subscriber = SubscriberFactory::new()->confirmed()->create();

    $this
        ->postJson(action(UnsubscribeController::class, $subscriber))
        ->assertSuccessful();

    expect($subscriber->refresh()->status)->toEqual(SubscriptionStatus::UNSUBSCRIBED);
});

it('cannot unsubscribe someone that is already unsubscribed', function () {
    $subscriber = SubscriberFactory::new()->unsubscribed()->create();

    $this
        ->postJson(action(UnsubscribeController::class, $subscriber))
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});
