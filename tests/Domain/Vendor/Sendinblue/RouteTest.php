<?php

use Illuminate\Support\Facades\Route;
use Spatie\WebhookClient\Exceptions\InvalidWebhookSignature;

beforeEach(function () {
    Route::sendinblueFeedback('sendinblue-feedback');

    config()->set('mailcoach.sendinblue_feedback.signing_secret', 'secret');
});

it('provides a route macro to handle webhooks', function () {
    $payload = getSendinblueStub('complaintWebhookContent.json');

    $this
        ->post('sendinblue-feedback?secret=secret', $payload)
        ->assertSuccessful();
});

it('fails when using an invalid payload', function () {
    $invalidPayload = getSendinblueStub('complaintWebhookContent.json');

    $this->post('sendinblue-feedback', $invalidPayload);
})->throws(InvalidWebhookSignature::class);
