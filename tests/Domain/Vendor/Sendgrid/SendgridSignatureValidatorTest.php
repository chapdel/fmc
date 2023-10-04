<?php

use Spatie\Mailcoach\Domain\Vendor\Sendgrid\SendgridSignatureValidator;
use Spatie\Mailcoach\Domain\Vendor\Sendgrid\SendgridWebhookConfig;

beforeEach(function () {
    $this->config = SendgridWebhookConfig::get();

    $this->validator = new SendgridSignatureValidator();
});

test('dummy test', function () {
    expect(true)->toBeTrue();
});
