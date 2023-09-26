<?php

use Spatie\Mailcoach\Domain\Campaign\Rules\DateTimeFieldRule;

it('passes if a valid date time is provided', function () {
    expect(rulePassed('datetime', [
        'date' => now()->addDay()->format('Y-m-d'),
        'hours' => '12',
        'minutes' => '15',
    ]))->toBeTrue();
});

it('doesnt pass if the input isnt an array', function () {
    expect(rulePassed('datetime', '2020-12-05 12:15'))->toBeFalse();
});

it('doesnt pass if the date is missing', function () {
    expect(rulePassed('datetime', [
        'hours' => '12',
        'minutes' => '15',
    ]))->toBeFalse();
});

it('doesnt pass if hours are missing', function () {
    expect(rulePassed('datetime', [
        'date' => now()->addDay()->format('Y-m-d'),
        'minutes' => '15',
    ]))->toBeFalse();
});

it('doesnt pass if minutes are missing', function () {
    expect(rulePassed('datetime', [
        'date' => now()->addDay()->format('Y-m-d'),
        'hours' => '12',
    ]))->toBeFalse();
});

it('doesnt passes if the date time is in the past', function () {
    expect(rulePassed('datetime', [
        'date' => now()->subDay()->format('Y-m-d'),
        'hours' => '12',
        'minutes' => '15',
    ]))->toBeFalse();
});

function rulePassed($attribute, $data): bool
{
    $passed = true;

    (new DateTimeFieldRule())->validate($attribute, $data, function () use (&$passed) {
        $passed = false;
    });

    return $passed;
}
