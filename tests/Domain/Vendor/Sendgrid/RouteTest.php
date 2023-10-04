<?php

use Illuminate\Support\Facades\Route;
use Spatie\WebhookClient\Exceptions\InvalidWebhookSignature;

beforeEach(function () {
    Route::sendgridFeedback('sendgrid-feedback');

    config()->set('mailcoach.sendgrid_feedback.signing_secret', 'secret');
});

it('provides a route macro to handle webhooks', function () {
    $this->withoutExceptionHandling();

    $payload = getSendgridStub('bouncePayload.json');

    $this
        ->post('sendgrid-feedback?secret=secret', $payload)
        ->assertSuccessful();
});

it('will not accept calls with an invalid signature', function () {
    $this->post('sendgrid-feedback?secret=incorrect_secret');
})->throws(InvalidWebhookSignature::class);
