<?php

use Spatie\Mailcoach\Domain\Shared\Commands\CheckLicenseCommand;

it('can check if the license is valid', function () {
    $this
        ->artisan(CheckLicenseCommand::class)
        ->assertExitCode(0);
});
