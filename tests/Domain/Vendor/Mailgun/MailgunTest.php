<?php

use Spatie\Mailcoach\Domain\Vendor\Mailgun\Enums\EventType;
use Spatie\Mailcoach\Domain\Vendor\Mailgun\Mailgun;

beforeEach(function () {
    $this->loadEnvironmentVariables();

    $this->mailgun = new Mailgun(env('MAILGUN_API_KEY'), env('MAILGUN_DOMAIN'));
});

it('can determine an api key is valid', function () {
    $result = $this->mailgun->isValidApiKey();

    expect($result)->toBeTrue();
});

it('can determine an api key is invalid', function () {
    $result = (new Mailgun('invalid-key', env('MAILGUN_DOMAIN')))->isValidApiKey();

    expect($result)->toBeFalse();
});

it('can update the webhook settings', function () {
    $url = 'https://test-url.com/first';
    $this->mailgun->setupWebhook($url, [EventType::Opened]);

    expect($this->mailgun->hasWebhook(EventType::Opened))->toBeTrue();
    expect($this->mailgun->openTrackingEnabled())->toBeTrue();
    expect($this->mailgun->clickTrackingEnabled())->toBeFalse();
});

it('can enable and disable open tracking on the account', function () {
    $this->mailgun->enableOpenTracking();
    expect($this->mailgun->openTrackingEnabled())->toBeTrue();

    $this->mailgun->enableOpenTracking(false);
    expect($this->mailgun->openTrackingEnabled())->toBeFalse();
});

it('can enable and disable click tracking on the account', function () {
    $this->mailgun->enableClickTracking();
    expect($this->mailgun->clickTrackingEnabled())->toBeTrue();

    $this->mailgun->enableClickTracking(false);
    expect($this->mailgun->clickTrackingEnabled())->toBeFalse();
});

it('can delete webhooks', function () {
    $url = 'https://test-url.com/first';
    $this->mailgun->setupWebhook($url, [EventType::Clicked]);

    expect($this->mailgun->hasWebhook(EventType::Clicked))->toBeTrue();

    $this->mailgun->deleteWebhook(EventType::Clicked);

    expect($this->mailgun->hasWebhook(EventType::Clicked))->toBeFalse();
});
