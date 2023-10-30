<?php

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Vendor\Postmark\PostmarkSignatureValidator;
use Spatie\Mailcoach\Domain\Vendor\Postmark\PostmarkWebhookConfig;

beforeEach(function () {
    $this->config = PostmarkWebhookConfig::get();

    $this->config->signingSecret = 'my-secret';

    $this->validator = new PostmarkSignatureValidator();
});

it('requires signature data', function () {
    $request = new Request();

    $request->headers->set('mailcoach-signature', 'my-secret');

    expect($this->validator->isValid($request, $this->config))->toBeTrue();
});

it('fails if the signature is missing', function () {
    $request = new Request();

    expect($this->validator->isValid($request, $this->config))->toBeFalse();
});

it('fails if the signature is invalid', function () {
    $request = new Request();

    $request->headers->set('mailcoach-signature', 'incorrect-secret');

    expect($this->validator->isValid($request, $this->config))->toBeFalse();
});
