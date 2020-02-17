<?php

namespace Spatie\Mailcoach\Tests\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Models\TagSegment;
use Spatie\Mailcoach\Tests\TestCase;

class SegmentTest extends TestCase
{
    private EmailList $emailList;

    public function setUp(): void
    {
        parent::setUp();

        $this->emailList = factory(EmailList::class)->create();
    }

    /** @test */
    public function it_can_build_a_query_to_get_subscribers_with_certain_tags()
    {
        $subscriberWithoutTag = $this->createSubscriberWithTags('noTag@example.com', []);
        $subscriberWithOneTag = $this->createSubscriberWithTags('oneTag@example.com', ['tagA']);
        $subscriberWithManyTags = $this->createSubscriberWithTags('multipleTags@example.com', ['tagA', 'tagB']);

        $subscribers = (TagSegment::create(['name' => 'testSegment', 'email_list_id' => $this->emailList->id])
            ->syncPositiveTags(['tagA'])
            ->getSubscribersQuery()
            ->get());

        $this->assertArrayContainsSubscribers([
            $subscriberWithOneTag,
            $subscriberWithManyTags,
        ], $subscribers);
    }

    /** @test */
    public function it_can_segment_on_subscribers_having_any_of_multiple_tags()
    {
        $subscriberWithoutTag = $this->createSubscriberWithTags('noTag@example.com', []);
        $subscriberWithOneTag = $this->createSubscriberWithTags('oneTag@example.com', ['tagA']);
        $subscriberWithManyTags = $this->createSubscriberWithTags('multipleTags@example.com', ['tagA', 'tagB']);
        $subscriberWithAllTags = $this->createSubscriberWithTags('allTags@example.com', ['tagA', 'tagB', 'tagC']);

        $subscribers = (TagSegment::create(['name' => 'testSegment', 'email_list_id' => $this->emailList->id])
            ->syncPositiveTags(['tagA', 'tagC'])
            ->getSubscribersQuery()
            ->get());

        $this->assertArrayContainsSubscribers([
            $subscriberWithOneTag,
            $subscriberWithManyTags,
            $subscriberWithAllTags
        ], $subscribers);
    }

    /** @test */
    public function it_can_segment_on_subscribers_having_all_of_the_given_multiple_tags()
    {
        Mail::fake();

        $subscriberWithoutTag = $this->createSubscriberWithTags('noTag@example.com', []);
        $subscriberWithTagA = $this->createSubscriberWithTags('tagA@example.com', ['tagA']);
        $subscriberWithTagB = $this->createSubscriberWithTags('tagB@example.com', ['tagB']);
        $subscriberWithTagAAndB = $this->createSubscriberWithTags('tagAAndB@example.com', ['tagA', 'tagB']);
        $subscriberWithAllTags = $this->createSubscriberWithTags('allTags@example.com', ['tagA', 'tagB', 'tagC']);

        $subscribers = (TagSegment::create([
            'name' => 'testSegment',
            'email_list_id' => $this->emailList->id,
            'all_positive_tags_required' => true,
        ])
            ->syncPositiveTags(['tagA', 'tagC'])
            ->getSubscribersQuery()
            ->get());

        $this->assertArrayContainsSubscribers([
            $subscriberWithAllTags,
        ], $subscribers);
    }

    /** @test */
    public function it_can_segment_on_subscribers_not_having_a_tag()
    {
        $subscriberWithoutTag = $this->createSubscriberWithTags('noTag@example.com', []);
        $subscriberWithTagA = $this->createSubscriberWithTags('tagA@example.com', ['tagA']);
        $subscriberWithTagB = $this->createSubscriberWithTags('tagB@example.com', ['tagB']);
        $subscriberWithManyTags = $this->createSubscriberWithTags('tagAandTagB@example.com', ['tagA', 'tagB']);

        $subscribers = (TagSegment::create([
            'name' => 'testSegment',
            'email_list_id' => $this->emailList->id,
        ])
            ->syncNegativeTags(['tagB'])
            ->getSubscribersQuery()
            ->get());


        $this->assertArrayContainsSubscribers([
            $subscriberWithoutTag,
            $subscriberWithTagA
        ], $subscribers);
    }

    /** @test */
    public function it_can_segment_on_subscribers_not_having_multiple_tags()
    {
        $subscriberWithoutTag = $this->createSubscriberWithTags('noTag@example.com', []);
        $subscriberWithTagA = $this->createSubscriberWithTags('tagA@example.com', ['tagA']);
        $subscriberWithTagB = $this->createSubscriberWithTags('tagB@example.com', ['tagB']);
        $subscriberWithManyTags = $this->createSubscriberWithTags('tagAandTagB@example.com', ['tagA', 'tagB']);

        $subscribers = (TagSegment::create([
            'name' => 'testSegment',
            'email_list_id' => $this->emailList->id,
        ])
            ->syncNegativeTags(['tagA', 'tagB'])
            ->getSubscribersQuery()
            ->get());


        $this->assertArrayContainsSubscribers([
            $subscriberWithoutTag,
        ], $subscribers);
    }

    /** @test */
    public function it_can_segment_on_subscribers_not_having_all_given_tags()
    {
        $subscriberWithoutTag = $this->createSubscriberWithTags('noTag@example.com', []);
        $subscriberWithTagA = $this->createSubscriberWithTags('tagA@example.com', ['tagA']);
        $subscriberWithTagB = $this->createSubscriberWithTags('tagB@example.com', ['tagB']);
        $subscriberWithManyTags = $this->createSubscriberWithTags('tagAandTagB@example.com', ['tagA', 'tagB']);

        $subscribers = (TagSegment::create([
            'name' => 'testSegment',
            'email_list_id' => $this->emailList->id,
            'all_negative_tags_required' => true
        ])
            ->syncNegativeTags(['tagA', 'tagB'])
            ->getSubscribersQuery()
            ->get());

        $this->assertArrayContainsSubscribers([
            $subscriberWithoutTag,
            $subscriberWithTagA,
            $subscriberWithTagB
        ], $subscribers);
    }

    /** @test */
    public function it_can_segment_on_positive_and_negative_segments_in_one_go()
    {
        $subscriberWithoutTag = $this->createSubscriberWithTags('noTag@example.com', []);
        $subscriberWithTagA = $this->createSubscriberWithTags('tagA@example.com', ['tagA']);
        $subscriberWithTagB = $this->createSubscriberWithTags('tagB@example.com', ['tagB']);
        $subscriberWithManyTags = $this->createSubscriberWithTags('tagAandTagB@example.com', ['tagA', 'tagB', 'tagC']);

        $subscribers = (TagSegment::create([
            'name' => 'testSegment',
            'email_list_id' => $this->emailList->id,
        ])
            ->syncPositiveTags(['tagA', 'tagB'])
            ->syncNegativeTags([ 'tagC'])
            ->getSubscribersQuery()
            ->get());

        $this->assertArrayContainsSubscribers([
            $subscriberWithTagA,
            $subscriberWithTagB
        ], $subscribers);
    }

    /** @test */
    public function it_can_segment_on_positive_and_negative_segments_all_required_in_one_go()
    {
        $subscriber1 = $this->createSubscriberWithTags('noTag@example.com', []);
        $subscriber2 = $this->createSubscriberWithTags('tagA@example.com', ['tagA']);
        $subscriber3 = $this->createSubscriberWithTags('tagABB@example.com', ['tagA', 'tagB']);
        $subscriber4 = $this->createSubscriberWithTags('tagABC@example.com', ['tagA', 'tagB', 'tagC']);
        $subscriber5 = $this->createSubscriberWithTags('tagABCD@example.com', ['tagA', 'tagB', 'tagC', 'tagD']);

        $subscribers = (TagSegment::create([
            'name' => 'testSegment',
            'email_list_id' => $this->emailList->id,
            'all_positive_tags_required' => true,
            'all_negative_tags_required' => true,
        ])
            ->syncPositiveTags(['tagA', 'tagB'])
            ->syncNegativeTags([ 'tagC', 'tagD'])
            ->getSubscribersQuery()
            ->get());

        $this->assertArrayContainsSubscribers([
            $subscriber3,
            $subscriber4
        ], $subscribers);
    }

    protected function createSubscriberWithTags(string $email, array $tags = []): Subscriber
    {
        /** @var Subscriber $subscriber */
        $subscriber = factory(Subscriber::class)->create([
            'email' => $email,
            'email_list_id' => $this->emailList->id,
        ]);

        $subscriber->addTags($tags);

        return $subscriber->refresh();
    }

    protected function assertArrayContainsSubscribers(array $expectedSubscribers, Collection $actualSubscribers)
    {
        $expectedSubscribers = collect($expectedSubscribers);

        $expectedSubscribers->each(function (Subscriber $expectedSubscriber) use ($actualSubscribers) {
            $this->assertContains(
                $expectedSubscriber->id,
                $actualSubscribers->pluck('id')->toArray(),
                "Expected subscriber {$expectedSubscriber->email} not found in actual subscribers)"
            );
        });

        $actualSubscribers->each(function (Subscriber $actualSubscriber) use ($expectedSubscribers) {
            $this->assertContains(
                $actualSubscriber->id,
                $expectedSubscribers->pluck('id')->toArray(),
                "Actual subscriber {$actualSubscriber->email} not found in expected subscribers)"
            );
        });
    }
}
