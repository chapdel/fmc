<?php

namespace Spatie\Mailcoach\Tests\Domain\ConditionBuilder\Conditions\Subscribers;

use Spatie\Mailcoach\Database\Factories\SubscriberFactory;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberSubscribedAtQueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;

use function PHPUnit\Framework\assertTrue;

/** The key shouldn't change, this means db migrations */
it('has a key', function () {
    $condition = new SubscriberSubscribedAtQueryCondition();

    expect($condition->key())->toBe('subscriber_subscribed_at');
});

it('can check a smaller than operator', function () {
    $condition = new SubscriberSubscribedAtQueryCondition();

    $subscriberA = SubscriberFactory::new()->create(['subscribed_at' => '2023-01-01']);
    $subscriberB = SubscriberFactory::new()->create(['subscribed_at' => '2023-01-02']);
    $subscriberC = SubscriberFactory::new()->create(['subscribed_at' => '2023-01-03']);

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::SmallerThanOrEquals,
        value: '2021-12-31',
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberC->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::SmallerThanOrEquals,
        value: '2023-01-01',
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberC->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::SmallerThanOrEquals,
        value: '2023-01-02',
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberC->id));
});

it('can check a greater than operator', function () {
    $condition = new SubscriberSubscribedAtQueryCondition();

    $subscriberA = SubscriberFactory::new()->create(['subscribed_at' => '2023-01-01']);
    $subscriberB = SubscriberFactory::new()->create(['subscribed_at' => '2023-01-02']);
    $subscriberC = SubscriberFactory::new()->create(['subscribed_at' => '2023-01-03']);

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::GreaterThanOrEquals,
        value: '2021-12-31',
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));
    assertTrue($query->pluck('id')->contains($subscriberC->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::GreaterThanOrEquals,
        value: '2023-01-01',
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));
    assertTrue($query->pluck('id')->contains($subscriberC->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::GreaterThanOrEquals,
        value: '2023-01-02',
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));
    assertTrue($query->pluck('id')->contains($subscriberC->id));
});

it('can check an in between operator', function () {
    $condition = new SubscriberSubscribedAtQueryCondition();

    $subscriberA = SubscriberFactory::new()->create(['subscribed_at' => '2023-01-01']);
    $subscriberB = SubscriberFactory::new()->create(['subscribed_at' => '2023-01-02']);
    $subscriberC = SubscriberFactory::new()->create(['subscribed_at' => '2023-01-03']);

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::Between,
        value: ['2022-12-31', '2023-01-01'],
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberC->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::Between,
        value: ['2023-01-01', '2023-01-02'],
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberC->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::Between,
        value: ['2023-01-02', '2023-01-03'],
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));
    assertTrue($query->pluck('id')->contains($subscriberC->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::Between,
        value: ['2023-01-02', '2023-01-04'],
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));
    assertTrue($query->pluck('id')->contains($subscriberC->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::Between,
        value: ['2023-01-03', '2023-01-04'],
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));
    assertTrue($query->pluck('id')->contains($subscriberC->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::Between,
        value: ['2023-01-04', '2023-01-05'],
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberC->id));
});

it('cannot use a non supported operator', function () {
    $condition = new SubscriberSubscribedAtQueryCondition();

    $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::Equals,
        value: '2023-01-02',
    );
})->throws('Operator `equals` is not allowed for condition `Subscriber Subscribed At`.');
