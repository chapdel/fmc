<?php

namespace Spatie\Mailcoach\Tests\Domain\Shared;

use Spatie\Mailcoach\Domain\Shared\Support\License\License;
use Spatie\Mailcoach\Tests\TestCase;

class LicenseTest extends TestCase
{
    /** @test */
    public function it_can_determine_that_there_is_no_license()
    {
        $this->assertNotNull((new License())->getStatus());
    }
}
