<?php

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberEmailQueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;

use function PHPUnit\Framework\assertTrue;

/** The key shouldn't change, this means db migrations */
it('has a key', function () {
    $condition = new SubscriberEmailQueryCondition();

    expect($condition->key())->toBe('subscriber_email');
});

it('can compare with an ends with operator', function () {
    $condition = new SubscriberEmailQueryCondition();

    $subscriberA = Subscriber::factory()->create(['email' => 'example@spatie.be']);
    $subscriberB = Subscriber::factory()->create(['email' => 'example@somethingElse.be']);

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::EndsWith,
        value: '@spatie.be',
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::EndsWith,
        value: '@somethingElse.be',
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));
});

it('can compare with a starts with operator', function () {
    $condition = new SubscriberEmailQueryCondition();

    $subscriberA = Subscriber::factory()->create(['email' => 'niels.example@spatie.be']);
    $subscriberB = Subscriber::factory()->create(['email' => 'iels.example@spatie.be']);

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::StartsWith,
        value: 'niels',
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::StartsWith,
        value: 'iels',
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));
});

it('cannot use a non supported operator', function () {
    $condition = new SubscriberEmailQueryCondition();

    $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::In,
        value: 'niels',
    );
})->throws('Operator `in` is not allowed for condition `Subscriber Email`.');
