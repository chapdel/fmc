<?php

use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::mailgunFeedback('mailgun-feedback');
});

it('provides a route macro to handle webhooks', function () {
    $invalidPayload = getStub('complaintWebhookContent');

    $validPayload = addValidSignature($invalidPayload);

    $this
        ->post('mailgun-feedback', $validPayload)
        ->assertSuccessful();
});

it('fails when using an invalid payload', function () {
    $invalidPayload = getStub('complaintWebhookContent');

    $this
        ->post('mailgun-feedback', $invalidPayload)
        ->assertStatus(406);
});
