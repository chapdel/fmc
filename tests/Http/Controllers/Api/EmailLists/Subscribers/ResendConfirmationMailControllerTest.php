<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Mails\ConfirmSubscriberMail;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers\ResendConfirmationMailController;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->withExceptionHandling();

    test()->loginToApi();

    Mail::fake();
});

it('can resend the confirmation mail', function () {
    $subscriber = SubscriberFactory::new()->unconfirmed()->create();

    $this
        ->postJson(action(ResendConfirmationMailController::class, $subscriber))
        ->assertSuccessful();

    Mail::assertQueued(ConfirmSubscriberMail::class);
});

it('cannot resend the confirmation mail to a subscriber that is not unconfirmed', function () {
    $subscriber = SubscriberFactory::new()->confirmed()->create();

    $this
        ->postJson(action(ResendConfirmationMailController::class, $subscriber))
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    Mail::assertNotQueued(ConfirmSubscriberMail::class);
});
