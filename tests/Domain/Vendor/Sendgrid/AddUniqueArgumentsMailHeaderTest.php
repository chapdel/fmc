<?php

namespace Spatie\Mailcoach\Domain\Vendor\Sendgrid\Tests;

use Spatie\Mailcoach\Domain\Vendor\Sendgrid\Actions\AddUniqueArgumentsMailHeader;

class AddUniqueArgumentsMailHeaderTest extends TestCase
{
    /** @test */
    public function the_listener_does_not_contain_syntax_errors()
    {
        new AddUniqueArgumentsMailHeader();

        $this->assertTrue(true);
    }
}
