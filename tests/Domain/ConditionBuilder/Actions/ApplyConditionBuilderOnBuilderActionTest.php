<?php

namespace Spatie\Mailcoach\Tests\Domain\ConditionBuilder\Actions;

use Spatie\Mailcoach\Database\Factories\SubscriberFactory;
use Spatie\Mailcoach\Database\Factories\TagFactory;
use Spatie\Mailcoach\Database\Factories\TagSegmentFactory;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\ConditionBuilder\Actions\ApplyConditionBuilderOnBuilderAction;
use Spatie\Mailcoach\Domain\ConditionBuilder\Collections\StoredConditionCollection;
use Spatie\Mailcoach\Domain\ConditionBuilder\ValueObjects\StoredCondition;

it('can build a query from a stored condition collection', function () {
    $action = app(ApplyConditionBuilderOnBuilderAction::class);

    $tag1 = TagFactory::new()->create(['name' => 'Flare']);
    $tag2 = TagFactory::new()->create(['name' => 'Mailcoach']);

    $collection = new StoredConditionCollection([
        StoredCondition::make('subscriber_tags', 'in', $tag1->id),
        StoredCondition::make('subscriber_tags', 'not-in', $tag2->id),
    ]);

    $builder = $action->execute(Subscriber::query(), $collection);

    test()->assertFalse($builder->exists());

    /** @var TagSegment $segment */
    $segment = TagSegmentFactory::new()->create();

    test()->assertFalse($builder->exists());

    $segment->delete();

    /** @var Subscriber $subscriber */
    $subscriber = SubscriberFactory::new()->create();
    $subscriber->tags()->attach($tag1);

    /** @var TagSegment $segment */
    $segment = TagSegmentFactory::new()->create();

    $builder = $action->execute(Subscriber::query(), $collection);

    test()->assertTrue($builder->exists());
});
