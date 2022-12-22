<?php

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\AttributeCondition;

it('has valid checks', function ($attribute, $comparison, $value, $result) {
    $automation = Automation::factory()->create();
    $subscriber = Subscriber::factory()->create([
        'extra_attributes' => [
            'attribute' => $attribute,
        ],
    ]);

    $condition = new AttributeCondition($automation, $subscriber, [
        'attribute' => 'attribute',
        'comparison' => $comparison,
        'value' => $value,
    ]);

    expect($condition->check())->toBe($result);
})->with([
    ['NL', '=', 'NL', true],
    ['NL', '!=', 'NL', false],

    ['1', '>=', '0', true],
    ['1', '>', '0', true],
    ['1', '<=', '0', false],
    ['1', '<', '0', false],
    ['0', '=', '0', true],

    ['', 'empty', '', true],
    ['0', 'empty', '', false],
    ['one', 'empty', '', false],

    ['', 'not_empty', '', false],
    ['0', 'not_empty', '', true],
    ['one', 'not_empty', '', true],

    ['', '=', '', true],
    ['one', '<', 'two', false],

    ['2022-01-01', '<', '2022-01-02', true],
    ['11:00', '<', '12:00', true],
    ['12:00', '=', '12:00', true],
    ['12:00', '>=', '12:00', true],
    ['12:00', '<=', '12:00', true],
    ['2022-01-02 11:00:00', '<', '2022-01-02 12:00:00', true],
]);
