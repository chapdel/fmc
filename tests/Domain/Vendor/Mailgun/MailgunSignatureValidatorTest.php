<?php

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Vendor\Mailgun\MailgunSignatureValidator;
use Spatie\Mailcoach\Domain\Vendor\Mailgun\MailgunWebhookConfig;

beforeEach(function () {
    $this->config = MailgunWebhookConfig::get();

    $this->validator = new MailgunSignatureValidator();
});

function validParams(array $overrides = []): array
{
    return array_merge(addValidSignature([]), $overrides);
}

it('requires signature data', function () {
    $request = new Request(validParams());

    expect($this->validator->isValid($request, $this->config))->toBeTrue();
});

it('fails if signature is missing', function () {
    $request = new Request(validParams([
        'signature' => [],
    ]));

    expect($this->validator->isValid($request, $this->config))->toBeFalse();
});

it('fails if data is missing', function () {
    $request = new Request(validParams([
        'event-data' => [],
    ]));

    expect($this->validator->isValid($request, $this->config))->toBeFalse();
});

it('fails if signature is invalid', function () {
    $request = new Request(validParams([
        'signature' => [
            'timestamp' => '1529006854',
            'token' => 'a8ce0edb2dd8301dee6c2405235584e45aa91d1e9f979f3de0',
            'signature' => hash_hmac(
                'sha256',
                sprintf('%s%s', '1529006854', 'a8ce0edb2dd8301dee6c2405235584e45aa91d1e9f979f3de0'),
                'a-wrong-signing-secret'
            ),
        ],
    ]));

    expect($this->validator->isValid($request, $this->config))->toBeFalse();
});
