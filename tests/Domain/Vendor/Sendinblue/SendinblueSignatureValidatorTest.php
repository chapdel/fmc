<?php

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Vendor\Sendinblue\SendinblueSignatureValidator;
use Spatie\Mailcoach\Domain\Vendor\Sendinblue\SendinblueWebhookConfig;

beforeEach(function () {
    config()->set('mailcoach.sendinblue_feedback.signing_secret', '1234');
    $this->config = SendinblueWebhookConfig::get();

    $this->validator = new SendinblueSignatureValidator();
});

it('requires a valid secret', function () {
    $request = Request::create('/?secret=1234');
    $request2 = Request::create('/?secret=123');

    expect($this->validator->isValid($request, $this->config))->toBeTrue();
    expect($this->validator->isValid($request2, $this->config))->toBeFalse();
});
