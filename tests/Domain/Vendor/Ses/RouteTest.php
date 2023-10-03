<?php

use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::sesFeedback('ses-feedback');
});

it('provides a route macro to handle webhooks', function () {
    $validPayload = getSesStub('bounceWebhookContent.json');

    $response = $this->postJson('ses-feedback', $validPayload);

    $this->assertNotEquals(404, $response->getStatusCode());
});
