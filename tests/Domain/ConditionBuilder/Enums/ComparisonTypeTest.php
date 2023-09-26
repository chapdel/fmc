<?php

use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;

it('can be created from a name', function () {
    $comparison = ComparisonOperator::fromName('equals');

    expect($comparison->value)->toBe('equals');
});

it('can be created from a name with different casing', function () {
    $comparison = ComparisonOperator::fromName('EQUALS');

    expect($comparison->value)->toBe('equals');
});

it('cannot be created from an unknown name', function () {
    $comparison = ComparisonOperator::fromName('unknown');

    expect($comparison->value)->toBe('equals');
})->throws(RuntimeException::class, 'Comparison operator with name `unknown` not found.');

it('can return labels', function () {
    expect(ComparisonOperator::labels())->toBe([
        'equals' => 'Equals To',
        'not-equals' => 'Not Equals To',
        'in' => 'Has One Of',
        'not-in' => 'Has None Of',
        'all' => 'Contains All',
        'none' => 'Contains None',
        'any' => 'Contains Any',
        'greater-than-or-equals' => 'Before',
        'smaller-than-or-equals' => 'After',
        'between' => 'Between',
        'ends-with' => 'Ends With',
        'starts-with' => 'Starts With',
    ]);
});

it('can return an option', function () {
    expect(ComparisonOperator::NotEquals->toOption())->toBe(['not-equals' => 'Not Equals To']);
});
