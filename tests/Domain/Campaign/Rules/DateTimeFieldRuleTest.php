<?php

use Spatie\Mailcoach\Domain\Campaign\Rules\DateTimeFieldRule;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

it('passes if a valid date time is provided', function () {
    test()->assertTrue(
        (new DateTimeFieldRule())->passes('datetime', [
            'date' => now()->addDay()->format('Y-m-d'),
            'hours' => '12',
            'minutes' => '15',
        ])
    );
});

it('doesnt pass if the input isnt an array', function () {
    test()->assertFalse(
        (new DateTimeFieldRule())->passes('datetime', '2020-12-05 12:15')
    );
});

it('doesnt pass if the date is missing', function () {
    test()->assertFalse(
        (new DateTimeFieldRule())->passes('datetime', [
            'hours' => '12',
            'minutes' => '15',
        ])
    );
});

it('doesnt pass if hours are missing', function () {
    test()->assertFalse(
        (new DateTimeFieldRule())->passes('datetime', [
            'date' => now()->addDay()->format('Y-m-d'),
            'minutes' => '15',
        ])
    );
});

it('doesnt pass if minutes are missing', function () {
    test()->assertFalse(
        (new DateTimeFieldRule())->passes('datetime', [
            'date' => now()->addDay()->format('Y-m-d'),
            'hours' => '12',
        ])
    );
});

it('doesnt passes if the date time is in the past', function () {
    test()->assertFalse(
        (new DateTimeFieldRule())->passes('datetime', [
            'date' => now()->subDay()->format('Y-m-d'),
            'hours' => '12',
            'minutes' => '15',
        ])
    );
});
