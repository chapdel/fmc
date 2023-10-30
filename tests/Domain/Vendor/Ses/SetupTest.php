<?php

use Spatie\Mailcoach\Domain\Vendor\Ses\MailcoachSes;
use Spatie\Mailcoach\Domain\Vendor\Ses\MailcoachSesConfig;

beforeEach(function () {
    $this->loadEnvironmentVariables();

    $config = new MailcoachSesConfig(
        env('AWS_ACCESS_KEY_ID'),
        env('AWS_SECRET_ACCESS_KEY'),
        env('AWS_DEFAULT_REGION'),
        'https://spatie.be/ses-feedback',
    );

    $this->mailcoachSes = new MailcoachSes($config);

    $this->mailcoachSes->uninstall();
});

it('can configure an AWS account for use with Mailcoach', function () {
    $this->mailcoachSes->install();

    expect($this->mailcoachSes->aws()->configurationSetExists('mailcoach'))->toBeTrue();
    expect($this->mailcoachSes->aws()->snsTopicExists('mailcoach'))->toBeTrue();
});

it('can remove the Mailcoach configuration for an AWS account', function () {
    $this->mailcoachSes->install();

    $this->mailcoachSes->uninstall();
    expect($this->mailcoachSes->aws()->configurationSetExists('mailcoach'))->toBeFalse();
    expect($this->mailcoachSes->aws()->snsTopicExists('mailcoach'))->toBeFalse();
});
