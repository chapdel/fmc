<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberOpenedCampaignQueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Domain\ConditionBuilder\ValueObjects\StoredCondition;

beforeEach(function () {
    test()->emailList = EmailList::factory()->create();
});

it('can build a query to get subscribers with certain tags', function () {
    $subscriberWithoutTag = createSubscriberWithTags('noTag@example.com', []);
    $subscriberWithOneTag = createSubscriberWithTags('oneTag@example.com', ['tagA']);
    $subscriberWithManyTags = createSubscriberWithTags('multipleTags@example.com', ['tagA', 'tagB']);

    /** @var TagSegment $segment */
    $segment = TagSegment::create(['name' => 'testSegment', 'email_list_id' => test()->emailList->id]);

    $segment->stored_conditions
        ->addSubscriberTags($subscriberWithOneTag->tags->first()->id);

    $segment->save();

    $subscribers = $segment
        ->getSubscribersQuery()
        ->get();

    assertArrayContainsSubscribers([
        $subscriberWithOneTag,
        $subscriberWithManyTags,
    ], $subscribers);
});

it('will only build a query for the email list', function () {
    $subscriberWithOneTag = createSubscriberWithTags('oneTag@example.com', ['tagA']);
    $otherListSubscriberWithTag = createSubscriberWithTags('otherTag@example.com', ['tagA'], EmailList::factory()->create()->id);

    /** @var TagSegment $segment */
    $segment = TagSegment::create(['name' => 'testSegment', 'email_list_id' => test()->emailList->id]);

    $segment->stored_conditions
        ->add(StoredCondition::make(
            key: SubscriberOpenedCampaignQueryCondition::KEY,
            comparisonOperator: ComparisonOperator::NotEquals,
            value: 1,
        ));

    $segment->save();

    $subscribers = $segment
        ->getSubscribersQuery()
        ->get();

    assertArrayContainsSubscribers([
        $subscriberWithOneTag,
    ], $subscribers);
});

it('can segment on subscribers having any of multiple tags', function () {
    $subscriberWithoutTag = createSubscriberWithTags('noTag@example.com', []);
    $subscriberWithOneTag = createSubscriberWithTags('oneTag@example.com', ['tagA']);
    $subscriberWithManyTags = createSubscriberWithTags('multipleTags@example.com', ['tagA', 'tagB']);
    $subscriberWithAllTags = createSubscriberWithTags('allTags@example.com', ['tagA', 'tagB', 'tagC']);

    /** @var TagSegment $segment */
    $segment = TagSegment::create(['name' => 'testSegment', 'email_list_id' => test()->emailList->id]);

    $segment->stored_conditions->addSubscriberTags([
        $subscriberWithOneTag->tags->first()->id,
        $subscriberWithAllTags->tags->last()->id,
    ]);

    $subscribers = $segment
        ->getSubscribersQuery()
        ->get();

    assertArrayContainsSubscribers([
        $subscriberWithOneTag,
        $subscriberWithManyTags,
        $subscriberWithAllTags,
    ], $subscribers);
});

it('can segment on subscribers having all of the given multiple tags', function () {
    Mail::fake();

    $subscriberWithoutTag = createSubscriberWithTags('noTag@example.com', []);
    $subscriberWithTagA = createSubscriberWithTags('tagA@example.com', ['tagA']);
    $subscriberWithTagB = createSubscriberWithTags('tagB@example.com', ['tagB']);
    $subscriberWithTagAAndB = createSubscriberWithTags('tagAAndB@example.com', ['tagA', 'tagB']);
    $subscriberWithAllTags = createSubscriberWithTags('allTags@example.com', ['tagA', 'tagB', 'tagC']);

    /** @var TagSegment $segment */
    $segment = TagSegment::create(
        [
            'name' => 'testSegment',
            'email_list_id' => test()->emailList->id,
        ]
    );

    $segment->stored_conditions->addSubscriberTags([
        $subscriberWithTagA->tags->first()->id,
        $subscriberWithAllTags->tags->last()->id,
    ], ComparisonOperator::All);

    $subscribers = $segment
        ->getSubscribersQuery()
        ->get();

    assertArrayContainsSubscribers([
        $subscriberWithAllTags,
    ], $subscribers);
});

it('can segment on subscribers not having a tag', function () {
    $subscriberWithoutTag = createSubscriberWithTags('noTag@example.com', []);
    $subscriberWithTagA = createSubscriberWithTags('tagA@example.com', ['tagA']);
    $subscriberWithTagB = createSubscriberWithTags('tagB@example.com', ['tagB']);
    $subscriberWithManyTags = createSubscriberWithTags('tagAandTagB@example.com', ['tagA', 'tagB']);

    /** @var TagSegment $segment */
    $segment = TagSegment::create([
        'name' => 'testSegment',
        'email_list_id' => test()->emailList->id,
    ]);

    $segment->stored_conditions
        ->addSubscriberTags($subscriberWithTagB->tags->first()->id, ComparisonOperator::NotIn);

    $subscribers = $segment
        ->getSubscribersQuery()
        ->get();

    assertArrayContainsSubscribers([
        $subscriberWithoutTag,
        $subscriberWithTagA,
    ], $subscribers);
});

it('can segment on subscribers not having multiple tags', function () {
    $subscriberWithoutTag = createSubscriberWithTags('noTag@example.com', []);
    $subscriberWithTagA = createSubscriberWithTags('tagA@example.com', ['tagA']);
    $subscriberWithTagB = createSubscriberWithTags('tagB@example.com', ['tagB']);
    $subscriberWithManyTags = createSubscriberWithTags('tagAandTagB@example.com', ['tagA', 'tagB']);

    /** @var TagSegment $segment */
    $segment = TagSegment::create([
        'name' => 'testSegment',
        'email_list_id' => test()->emailList->id,
    ]);

    $segment->stored_conditions->addSubscriberTags([
        $subscriberWithTagA->tags->first()->id,
        $subscriberWithTagB->tags->first()->id,
    ], ComparisonOperator::NotIn);

    $subscribers = $segment
        ->getSubscribersQuery()
        ->get();

    assertArrayContainsSubscribers([
        $subscriberWithoutTag,
    ], $subscribers);
});

it('can segment on subscribers not having all given tags', function () {
    $subscriberWithoutTag = createSubscriberWithTags('noTag@example.com', []);
    $subscriberWithTagA = createSubscriberWithTags('tagA@example.com', ['tagA']);
    $subscriberWithTagB = createSubscriberWithTags('tagB@example.com', ['tagB']);
    $subscriberWithManyTags = createSubscriberWithTags('tagAandTagB@example.com', ['tagA', 'tagB']);

    /** @var TagSegment $segment */
    $segment = TagSegment::create([
        'name' => 'testSegment',
        'email_list_id' => test()->emailList->id,
    ]);

    $segment->stored_conditions->addSubscriberTags([
        $subscriberWithTagA->tags->first()->id,
        $subscriberWithTagB->tags->first()->id,
    ], ComparisonOperator::None);

    $subscribers = $segment
        ->getSubscribersQuery()
        ->get();

    assertArrayContainsSubscribers([
        $subscriberWithoutTag,
        $subscriberWithTagA,
        $subscriberWithTagB,
    ], $subscribers);
});

it('can segment on positive and negative segments in one go', function () {
    $subscriberWithoutTag = createSubscriberWithTags('noTag@example.com', []);
    $subscriberWithTagA = createSubscriberWithTags('tagA@example.com', ['tagA']);
    $subscriberWithTagB = createSubscriberWithTags('tagB@example.com', ['tagB']);
    $subscriberWithManyTags = createSubscriberWithTags('tagAandTagB@example.com', ['tagA', 'tagB', 'tagC']);

    /** @var TagSegment $segment */
    $segment = TagSegment::create([
        'name' => 'testSegment',
        'email_list_id' => test()->emailList->id,
    ]);

    $segment->stored_conditions
        ->addSubscriberTags(
            [
                $subscriberWithTagA->tags->first()->id,
                $subscriberWithTagB->tags->first()->id,
            ],
            ComparisonOperator::In
        )
        ->addSubscriberTags(
            [
                $subscriberWithManyTags->tags->last()->id,
            ],
            ComparisonOperator::NotIn
        );

    $subscribers = $segment
        ->getSubscribersQuery()
        ->get();

    assertArrayContainsSubscribers([
        $subscriberWithTagA,
        $subscriberWithTagB,
    ], $subscribers);
});

it('can segment on positive and negative segments all required in one go', function () {
    $subscriber1 = createSubscriberWithTags('noTag@example.com', []);
    $subscriber2 = createSubscriberWithTags('tagA@example.com', ['tagA']);
    $subscriber3 = createSubscriberWithTags('tagABB@example.com', ['tagA', 'tagB']);
    $subscriber4 = createSubscriberWithTags('tagABC@example.com', ['tagA', 'tagB', 'tagC']);
    $subscriber5 = createSubscriberWithTags('tagABCD@example.com', ['tagA', 'tagB', 'tagC', 'tagD']);

    /** @var TagSegment $segment */
    $segment = TagSegment::create([
        'name' => 'testSegment',
        'email_list_id' => test()->emailList->id,
    ]);

    $segment->stored_conditions
        ->addSubscriberTags($subscriber3->tags->pluck('id')->toArray(), ComparisonOperator::All)
        ->addSubscriberTags($subscriber5->tags->reverse()->slice(0, 2)->pluck('id')->toArray(), ComparisonOperator::None);

    $subscribers = $segment
        ->getSubscribersQuery()
        ->get();

    assertArrayContainsSubscribers([
        $subscriber3,
        $subscriber4,
    ], $subscribers);
});

// Helpers
function createSubscriberWithTags(string $email, array $tags = [], int $emailListId = null): Subscriber
{
    /** @var Subscriber $subscriber */
    $subscriber = Subscriber::factory()->create([
        'email' => $email,
        'email_list_id' => $emailListId ?? test()->emailList->id,
    ]);

    $subscriber->addTags($tags);

    return $subscriber->refresh();
}

function assertArrayContainsSubscribers(array $expectedSubscribers, Collection $actualSubscribers): void
{
    $expectedSubscribers = collect($expectedSubscribers);

    $expectedSubscribers->each(function (Subscriber $expectedSubscriber) use ($actualSubscribers) {
        expect($actualSubscribers->pluck('id')->toArray())->toContain($expectedSubscriber->id);
    });

    $actualSubscribers->each(function (Subscriber $actualSubscriber) use ($expectedSubscribers) {
        expect($expectedSubscribers->pluck('id')->toArray())->toContain($actualSubscriber->id);
    });
}
