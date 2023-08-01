<?php

namespace Spatie\Mailcoach\Tests\Domain\ConditionBuilder\Conditions\Subscribers;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;
use Spatie\Mailcoach\Database\Factories\SubscriberFactory;
use Spatie\Mailcoach\Database\Factories\TagFactory;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberTagsQueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;

/** The key shouldn't change, this means db migrations */
it('has a key', function () {
    $condition = new SubscriberTagsQueryCondition();

    expect($condition->key())->toBe('subscriber_tags');
});

it('can check if a subscriber has a tag', function () {
    $condition = new SubscriberTagsQueryCondition();

    $tag1 = TagFactory::new()->create(['name' => 'tagA']);
    $tag2 = TagFactory::new()->create(['name' => 'tagB']);

    $subscriberA = SubscriberFactory::new()->create();
    $subscriberA->tags()->sync([$tag1->id, $tag2->id]);

    $subscriberB = Subscriber::factory()->create();

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::In,
        value: $tag1->id,
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::NotIn,
        value: $tag1->id,
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::All,
        value: $tag1->id,
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::None,
        value: $tag1->id,
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));
});

it('can check if all given tags are linked to the subscriber', function () {
    $condition = new SubscriberTagsQueryCondition();

    $tag1 = TagFactory::new()->create(['name' => 'tagA']);
    $tag2 = TagFactory::new()->create(['name' => 'tagB']);
    $tag3 = TagFactory::new()->create(['name' => 'tagC']);

    /** @var Subscriber $subscriber */
    $subscriber = SubscriberFactory::new()->create();
    $subscriber->tags()->sync([$tag1->id, $tag2->id, $tag3->id]);

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::In,
        value: [$tag1->id, $tag2->id],
    );

    assertTrue($query->exists());

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::NotIn,
        value: [$tag1->id, $tag2->id],
    );

    assertFalse($query->exists());

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::All,
        value: [$tag1->id, $tag2->id],
    );

    assertTrue($query->exists());

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::None,
        value: [$tag1->id, $tag2->id],
    );

    assertFalse($query->exists());
});

it('can check if only one given tag is linked to the subscriber, but more are given', function () {
    $condition = new SubscriberTagsQueryCondition();

    $tag1 = TagFactory::new()->create(['name' => 'tagA']);
    $tag2 = TagFactory::new()->create(['name' => 'tagB']);
    $tag3 = TagFactory::new()->create(['name' => 'tagC']);

    $subscriberA = SubscriberFactory::new()->create();
    $subscriberA->tags()->sync([$tag1->id, $tag2->id]);

    $subscriberB = SubscriberFactory::new()->create();

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::In,
        value: [$tag1->id, $tag3->id],
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::NotIn,
        value: [$tag1->id, $tag3->id],
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::All,
        value: [$tag1->id, $tag3->id],
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::None,
        value: [$tag1->id, $tag3->id],
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));
});

it('cannot use a non supported operator', function () {
    $condition = new SubscriberTagsQueryCondition();

    $condition->apply(
        baseQuery: TagSegment::query(),
        operator: ComparisonOperator::Equals,
        value: 'tagA',
    );
})->throws('Operator `equals` is not allowed for condition `Subscriber Tags`.');
