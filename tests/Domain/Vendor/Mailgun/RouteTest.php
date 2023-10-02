<?php

use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::mailgunFeedback('mailgun-feedback');
});

it('provides a route macro to handle webhooks', function () {
    $invalidPayload = getMailgunStub('complaintWebhookContent.json');

    $validPayload = addValidSignature($invalidPayload);

    $this
        ->post('mailgun-feedback', $validPayload)
        ->assertSuccessful();
});

it('fails when using an invalid payload', function () {
    $invalidPayload = getMailgunStub('complaintWebhookContent.json');

    $this
        ->post('mailgun-feedback', $invalidPayload)
        ->assertStatus(406);
});
