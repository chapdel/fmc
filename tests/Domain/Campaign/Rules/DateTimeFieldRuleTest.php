<?php

use Spatie\Mailcoach\Domain\Campaign\Rules\DateTimeFieldRule;

it('passes if a valid date time is provided', function () {
    test()->assertTrue(
        rulePassed('datetime', [
            'date' => now()->addDay()->format('Y-m-d'),
            'hours' => '12',
            'minutes' => '15',
        ])
    );
});

it('doesnt pass if the input isnt an array', function () {
    test()->assertFalse(
        rulePassed('datetime', '2020-12-05 12:15')
    );
});

it('doesnt pass if the date is missing', function () {
    test()->assertFalse(
        rulePassed('datetime', [
            'hours' => '12',
            'minutes' => '15',
        ])
    );
});

it('doesnt pass if hours are missing', function () {
    test()->assertFalse(
        rulePassed('datetime', [
            'date' => now()->addDay()->format('Y-m-d'),
            'minutes' => '15',
        ])
    );
});

it('doesnt pass if minutes are missing', function () {
    test()->assertFalse(
        rulePassed('datetime', [
            'date' => now()->addDay()->format('Y-m-d'),
            'hours' => '12',
        ])
    );
});

it('doesnt passes if the date time is in the past', function () {
    test()->assertFalse(
        rulePassed('datetime', [
            'date' => now()->subDay()->format('Y-m-d'),
            'hours' => '12',
            'minutes' => '15',
        ])
    );
});

function rulePassed($attribute, $data): bool
{
    $passed = true;

    (new DateTimeFieldRule())->validate($attribute, $data, function () use (&$passed) {
        $passed = false;
    });

    return $passed;
}
