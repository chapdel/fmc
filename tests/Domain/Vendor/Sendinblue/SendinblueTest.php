<?php

use Spatie\Mailcoach\Domain\Vendor\Sendinblue\Enums\EventType;
use Spatie\Mailcoach\Domain\Vendor\Sendinblue\Sendinblue;

beforeEach(function () {
    $this->loadEnvironmentVariables();

    $this->sendinblue = new Sendinblue(env('SENDINBLUE_API_KEY'));
});

it('can determine an api key is valid', function () {
    $result = $this->sendinblue->isValidApiKey();

    expect($result)->toBeTrue();
});

it('can determine an api key is invalid', function () {
    $result = (new Sendinblue('invalid-key'))->isValidApiKey();

    expect($result)->toBeFalse();
});

it('can update the webhook settings', function () {
    $url = 'https://test-url.com/first';
    $this->sendinblue->setupWebhook($url);

    $webhookSettings = $this->sendinblue->getWebhook($url);

    expect($webhookSettings['url'])->toBe($url)
        ->and(in_array(EventType::Open->value, $webhookSettings['events']))->toBeTrue()
        ->and(in_array(EventType::Click->value, $webhookSettings['events']))->toBeTrue()
        ->and(in_array(EventType::Bounce->value, $webhookSettings['events']))->toBeTrue()
        ->and(in_array(EventType::Spam->value, $webhookSettings['events']))->toBeTrue();
});

it('can delete webhooks', function () {
    $url = 'https://test-url.com/first';
    $this->sendinblue->setupWebhook($url);

    expect($this->sendinblue->getWebhook($url))->not()->toBeNull();

    $this->sendinblue->deleteWebhook($url);

    expect($this->sendinblue->getWebhook($url))->toBeNull();
})->skip('Timing out on CI');
