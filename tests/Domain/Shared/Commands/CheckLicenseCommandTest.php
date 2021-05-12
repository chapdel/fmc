<?php

namespace Spatie\Mailcoach\Tests\Domain\Shared\Commands;

use Spatie\Mailcoach\Domain\Shared\Commands\CheckLicenseCommand;
use Spatie\Mailcoach\Tests\TestCase;

class CheckLicenseCommandTest extends TestCase
{
    /** @test */
    public function it_can_check_if_the_license_is_valid()
    {
        $this
            ->artisan(CheckLicenseCommand::class)
            ->assertExitCode(0);
    }
}
