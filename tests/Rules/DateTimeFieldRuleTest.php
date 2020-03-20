<?php

namespace Spatie\Mailcoach\Tests\Rules;

use Spatie\Mailcoach\Rules\DateTimeFieldRule;
use Spatie\Mailcoach\Tests\TestCase;

class DateTimeFieldRuleTest extends TestCase
{
    /** @test */
    public function it_passes_if_a_valid_date_time_is_provided()
    {
        $this->assertTrue(
            (new DateTimeFieldRule())->passes('datetime', [
                'date' => now()->addDay()->format('Y-m-d'),
                'hours' => '12',
                'minutes' => '15',
            ])
        );
    }

    /** @test */
    public function it_doesnt_pass_if_the_input_isnt_an_array()
    {
        $this->assertFalse(
            (new DateTimeFieldRule())->passes('datetime', '2020-12-05 12:15')
        );
    }

    /** @test */
    public function it_doesnt_pass_if_the_date_is_missing()
    {
        $this->assertFalse(
            (new DateTimeFieldRule())->passes('datetime', [
                'hours' => '12',
                'minutes' => '15',
            ])
        );
    }

    /** @test */
    public function it_doesnt_pass_if_hours_are_missing()
    {
        $this->assertFalse(
            (new DateTimeFieldRule())->passes('datetime', [
                'date' => now()->addDay()->format('Y-m-d'),
                'minutes' => '15',
            ])
        );
    }

    /** @test */
    public function it_doesnt_pass_if_minutes_are_missing()
    {
        $this->assertFalse(
            (new DateTimeFieldRule())->passes('datetime', [
                'date' => now()->addDay()->format('Y-m-d'),
                'hours' => '12',
            ])
        );
    }

    /** @test */
    public function it_doesnt_passes_if_the_date_time_is_in_the_past()
    {
        $this->assertFalse(
            (new DateTimeFieldRule())->passes('datetime', [
                'date' => now()->subDay()->format('Y-m-d'),
                'hours' => '12',
                'minutes' => '15',
            ])
        );
    }
}
