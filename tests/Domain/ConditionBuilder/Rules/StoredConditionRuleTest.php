<?php

namespace Spatie\Mailcoach\Tests\Domain\ConditionBuilder\Rules;

use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use Spatie\Mailcoach\Domain\ConditionBuilder\Rules\StoredConditionRule;
use Spatie\Mailcoach\Domain\ConditionBuilder\ValueObjects\StoredCondition;

it('can validate a stored condition array', function () {
    $rule = new StoredConditionRule();

    $condition = StoredCondition::make('subscriber_tags', 'in', 'Flare');

    expect($rule)->toPassWith($condition->toArray());
});

it('cannot validate invalid structures', function (array $data) {
    $rule = new StoredConditionRule();

    expect($rule)->not()->toPassWith($data);
})->with([
    'missing condition key' => [
        'condition' => [
            'comparisonType' => 'in',
            'value' => '',
        ],
    ],
    'missing comparison type' => [
        'condition' => [
            'key' => 'subscriber_tags',
            'value' => '',
        ],
    ],
    'missing value' => [
        'condition' => [
            'key' => 'subscriber_tags',
            'comparisonType' => 'in',
        ],
    ],
    'invalid condition key' => [
        'condition' => [
            'key' => 123,
            'comparisonType' => 'equals',
            'value' => '',
        ],
    ],
    'invalid comparison type' => [
        'condition' => [
            'key' => 'subscriber_tags',
            'comparisonType' => 123,
            'value' => '',
        ],
    ],
]);

expect()->extend('toPassWith', function (mixed $value) {
    $rule = $this->value;

    if (! $rule instanceof ValidationRule) {
        throw new Exception('Value is not a valid rule');
    }

    $passed = true;

    $fail = function () use (&$passed) {
        $passed = false;
    };

    $rule->validate('attribute', $value, $fail);

    expect($passed)->toBeTrue();
});
