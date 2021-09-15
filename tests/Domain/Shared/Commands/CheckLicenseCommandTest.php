<?php

use Spatie\Mailcoach\Domain\Shared\Commands\CheckLicenseCommand;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

it('can check if the license is valid', function () {
    $this
        ->artisan(CheckLicenseCommand::class)
        ->assertExitCode(0);
});
