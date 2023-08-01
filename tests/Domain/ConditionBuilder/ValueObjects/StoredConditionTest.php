<?php

namespace Spatie\Mailcoach\Tests\Domain\ConditionBuilder\ValueObjects;

use Spatie\Mailcoach\Database\Factories\TagSegmentFactory;
use Spatie\Mailcoach\Domain\ConditionBuilder\Collections\StoredConditionCollection;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Condition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberTagsQueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Domain\ConditionBuilder\ValueObjects\StoredCondition;

it('can be created from a string', function () {
    $storedCondition = StoredCondition::make('subscriber_tags', 'in', 'Flare');

    expect($storedCondition)
        ->toBeInstanceOf(StoredCondition::class)
        ->and($storedCondition->condition)->toBeInstanceOf(Condition::class)
        ->and($storedCondition->comparisonOperator)->toBe(ComparisonOperator::In)
        ->and($storedCondition->value)->toBe('Flare');
});

it('can be created from objects', function () {
    $storedCondition = StoredCondition::make(
        new SubscriberTagsQueryCondition(),
        ComparisonOperator::In,
        'Flare'
    );

    expect($storedCondition)->toBeInstanceOf(StoredCondition::class);
});

it('can be created from a request', function () {
    $storedCondition = StoredCondition::fromRequest([
        'condition' => [
            'key' => 'subscriber_tags',
        ],
        'comparison_operator' => 'in',
        'value' => 'Flare',
    ]);

    expect($storedCondition)
        ->toBeInstanceOf(StoredCondition::class)
        ->and($storedCondition->condition)->toBeInstanceOf(Condition::class)
        ->and($storedCondition->comparisonOperator)->toBe(ComparisonOperator::In)
        ->and($storedCondition->value)->toBe('Flare');
});

it('can be created from a db array', function () {
    $storedCondition = StoredCondition::fromDb([
        'condition_key' => 'subscriber_tags',
        'comparison_operator' => 'in',
        'value' => 'Flare',
    ]);

    expect($storedCondition)
        ->toBeInstanceOf(StoredCondition::class)
        ->and($storedCondition->condition)->toBeInstanceOf(Condition::class)
        ->and($storedCondition->comparisonOperator)->toBe(ComparisonOperator::In)
        ->and($storedCondition->value)->toBe('Flare');
});

it('can be transformed to an array', function () {
    $storedCondition = StoredCondition::make('subscriber_tags', 'in', 'Flare');

    expect($storedCondition->toArray())->toBe([
        'condition' => [
            'key' => 'subscriber_tags',
            'label' => 'Subscriber Tags',
            'comparison_operators' => [
                'in' => 'Has One Of',
                'not-in' => 'Has None Of',
                'all' => 'Contains All',
                'none' => 'Contains None',
            ],
        ],
        'comparison_operator' => 'in',
        'value' => 'Flare',
    ]);
});

it('can be transformed to a db array', function () {
    $storedCondition = StoredCondition::make('subscriber_tags', 'in', 'Flare');

    expect($storedCondition->toDb())->toBe([
        'condition_key' => 'subscriber_tags',
        'comparison_operator' => 'in',
        'value' => 'Flare',
    ]);
});

it('can be stored and retrieved from a model', function () {
    $storedCondition = StoredCondition::make('subscriber_tags', 'in', 'Flare');

    $model = TagSegmentFactory::new()->create([
        'stored_conditions' => [$storedCondition],
    ]);

    expect($model->stored_conditions)->toBeInstanceOf(StoredConditionCollection::class)
        ->and($model->stored_conditions->first())->toBeInstanceOf(StoredCondition::class)
        ->and($model->stored_conditions->first()->condition->key())->toBe('subscriber_tags')
        ->and($model->stored_conditions->first()->comparisonOperator->value)->toBe('in')
        ->and($model->stored_conditions->first()->value)->toBe('Flare');
});
