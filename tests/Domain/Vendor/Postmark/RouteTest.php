<?php

use Illuminate\Support\Facades\Route;
use Spatie\WebhookClient\Exceptions\InvalidWebhookSignature;

beforeEach(function () {
    config()->set('mailcoach.postmark_feedback.signing_secret', 'my-secret');

    Route::postmarkFeedback('postmark-feedback');
});

it('provides a route macro to handle webhooks', function () {
    $payload = getPostmarkStub('complaintWebhookContent.json');

    $this
        ->post('postmark-feedback', $payload, ['mailcoach-signature' => 'my-secret'])
        ->assertSuccessful();
});

it('fails when using an invalid payload', function () {
    $payload = getPostmarkStub('complaintWebhookContent.json');

    $this->post('postmark-feedback', $payload);
})->throws(InvalidWebhookSignature::class);
