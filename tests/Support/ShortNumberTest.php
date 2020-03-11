<?php

namespace Spatie\Mailcoach\Tests\Support;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Tests\TestCase;

class ShortNumberTest extends TestCase
{
    /** @test */
    public function it_can_shorten_a_number()
    {
        $this->assertEquals('1', Str::shortNumber(1));
        $this->assertEquals('100', Str::shortNumber(100));
        $this->assertEquals('500', Str::shortNumber(500));
        $this->assertEquals('999', Str::shortNumber(999));
        $this->assertEquals('1K', Str::shortNumber(1000));
        $this->assertEquals('1.7K', Str::shortNumber(1799));

        $this->assertEquals('999.9K', Str::shortNumber(999_999));
        $this->assertEquals('1M', Str::shortNumber(1_000_000));

        $this->assertEquals('🤯', Str::shortNumber(1_000_000_000));
    }
}
