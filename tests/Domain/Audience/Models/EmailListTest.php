<?php

namespace Spatie\Mailcoach\Tests\Domain\Audience\Models;

use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Exceptions\CouldNotSubscribe;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\Campaign\Mails\ReconfirmationMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\InactiveSubscriber;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\CustomEmailList;
use Spatie\Mailcoach\Tests\TestClasses\CustomSubscriber;
use Spatie\TestTime\TestTime;

class EmailListTest extends TestCase
{
    protected EmailList $emailList;

    public function setUp(): void
    {
        parent::setUp();

        $this->emailList = EmailList::factory()->create(['name' => 'Mailcoach Subscribers']);
    }

    /** @test */
    public function it_can_add_a_subscriber_to_a_list()
    {
        $subscriber = $this->emailList->subscribe('john@example.com');

        $this->assertEquals('john@example.com', $subscriber->email);
    }

    /** @test */
    public function it_can_add_a_subscriber_with_extra_attributes_to_a_list()
    {
        $attributes = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'extra_attributes' => ['key 1' => 'Value 1', 'key 2' => 'Value 2'],
        ];

        $subscriber = $this->emailList->subscribe('john@example.com', $attributes)->refresh();

        $this->assertEquals('john@example.com', $subscriber->email);
        $this->assertEquals('John', $subscriber->first_name);
        $this->assertEquals('Doe', $subscriber->last_name);
        $this->assertEquals($attributes['extra_attributes'], $subscriber->extra_attributes->all());
    }

    /** @test */
    public function when_adding_someone_that_was_already_subscribed_no_new_subscription_will_be_created()
    {
        $this->emailList->subscribe('john@example.com');
        $this->emailList->subscribe('john@example.com');

        $this->assertEquals(1, Subscriber::count());
    }

    /** @test */
    public function it_can_unsubscribe_someone()
    {
        $this->emailList->subscribe('john@example.com');

        $this->assertTrue($this->emailList->unsubscribe('john@example.com'));
        $this->assertFalse($this->emailList->unsubscribe('non-existing-subscriber@example.com'));

        $this->assertEquals(SubscriptionStatus::UNSUBSCRIBED, Subscriber::first()->status);
    }

    /** @test */
    public function it_can_get_all_subscribers_that_are_subscribed()
    {
        $this->emailList->subscribe('john@example.com');
        $this->emailList->subscribe('jane@example.com');
        $this->emailList->unsubscribe('john@example.com');

        $subscribers = $this->emailList->subscribers;
        $this->assertCount(1, $subscribers);
        $this->assertEquals('jane@example.com', $subscribers->first()->email);

        $subscribers = $this->emailList->allSubscribers;
        $this->assertCount(2, $subscribers);
    }

    /** @test */
    public function it_can_subscribe_someone_immediately_even_if_double_opt_in_is_enabled()
    {
        Mail::fake();

        $this->emailList->update(['requires_confirmation' => true]);

        $this->emailList->subscribeSkippingConfirmation('john@example.com');

        Mail::assertNothingQueued();

        $this->assertEquals('john@example.com', $this->emailList->subscribers->first()->email);
    }

    /** @test */
    public function it_cannot_subscribe_an_invalid_email()
    {
        $this->expectException(CouldNotSubscribe::class);

        $this->emailList->subscribe('invalid-email');
    }

    /** @test */
    public function it_can_get_the_status_of_a_subscription()
    {
        $this->assertNull($this->emailList->getSubscriptionStatus('john@example.com'));

        $this->emailList->subscribe('john@example.com');

        $this->assertEquals(SubscriptionStatus::SUBSCRIBED, $this->emailList->getSubscriptionStatus('john@example.com'));
    }

    /** @test */
    public function it_can_summarize_an_email_list()
    {
        TestTime::freeze();

        $this->assertEquals([
            'total_number_of_subscribers' => 0,
            'total_number_of_subscribers_gained' => 0,
            'total_number_of_unsubscribes_gained' => 0,
        ], $this->emailList->summarize(now()->subWeek()));

        $subscriber = Subscriber::createWithEmail('john@example.com')
            ->skipConfirmation()
            ->subscribeTo($this->emailList);

        $this->assertEquals([
            'total_number_of_subscribers' => 1,
            'total_number_of_subscribers_gained' => 1,
            'total_number_of_unsubscribes_gained' => 0,
        ], $this->emailList->summarize(now()->subWeek()));

        $subscriber->unsubscribe();

        $this->assertEquals([
            'total_number_of_subscribers' => 0,
            'total_number_of_subscribers_gained' => 1,
            'total_number_of_unsubscribes_gained' => 1,
        ], $this->emailList->summarize(now()->subWeek()));

        Subscriber::createWithEmail('jane@example.com')
            ->skipConfirmation()
            ->subscribeTo($this->emailList);

        $this->assertEquals([
            'total_number_of_subscribers' => 1,
            'total_number_of_subscribers_gained' => 2,
            'total_number_of_unsubscribes_gained' => 1,
        ], $this->emailList->summarize(now()->subWeek()));

        TestTime::addWeek();

        $this->assertEquals([
            'total_number_of_subscribers' => 1,
            'total_number_of_subscribers_gained' => 0,
            'total_number_of_unsubscribes_gained' => 0,
        ], $this->emailList->summarize(now()->subWeek()));

        Subscriber::createWithEmail('paul@example.com')
            ->skipConfirmation()
            ->subscribeTo($this->emailList);

        $this->assertEquals([
            'total_number_of_subscribers' => 2,
            'total_number_of_subscribers_gained' => 1,
            'total_number_of_unsubscribes_gained' => 0,
        ], $this->emailList->summarize(now()->subWeek()));
    }

    /** @test */
    public function it_can_reference_tags_and_segments_when_using_a_custom_model()
    {
        Tag::factory(2)->create(['email_list_id' => $this->emailList->id]);
        TagSegment::create(['name' => 'testSegment', 'email_list_id' => $this->emailList->id]);

        Config::set("mailcoach.models.email_list", CustomEmailList::class);
        Config::set("mailcoach.models.subscriber", CustomSubscriber::class);

        $list = CustomEmailList::find($this->emailList->id);

        $this->assertEquals(2, $list->tags()->count());
        $this->assertEquals(1, $list->segments()->count());
    }

    /** @test * */
    public function it_can_send_a_cleanup_email_for_subscribers_that_havent_opened_campaigns()
    {
        Mail::fake();
        TestTime::setTestNow(now()->startOfSecond());

        $subscriber = $this->emailList->subscribeSkippingConfirmation('john@example.com');
        $subscriber2 = $this->emailList->subscribeSkippingConfirmation('jane@example.com');

        $campaign1 = Campaign::create()
            ->from('test@example.com')
            ->subject('test')
            ->content('my content')
            ->to($this->emailList)
            ->send();

        Campaign::create()
            ->from('test@example.com')
            ->subject('test')
            ->content('my content')
            ->to($this->emailList)
            ->send();

        $subscriber2->opens()->create([
            'campaign_id' => $campaign1->id,
            'send_id' => $subscriber2->sends()->first()->id,
        ]);

        $this->assertEquals(2, Campaign::count());
        $this->assertEquals(1, $subscriber2->opens()->count());
        $this->assertEquals(1, $campaign1->opens()->count());

        $this->emailList
            ->unsubscribeInactiveSubscribers()
            ->didNotOpenPastNumberOfCampaigns(2)
            ->unsubscribeAfter(CarbonInterval::days(2))
            ->sendReconfirmationMail();

        $this->assertEquals(1, InactiveSubscriber::count());
        tap(InactiveSubscriber::first(), function (InactiveSubscriber $inactiveSubscriber) use ($subscriber) {
            $this->assertEquals($subscriber->id, $inactiveSubscriber->subscriber_id);
            $this->assertEquals(now()->addDays(2), $inactiveSubscriber->unsubscribe_at);
        });

        Mail::assertQueued(ReconfirmationMail::class, function (ReconfirmationMail $reconfirmationMail) {
            $this->assertTrue($reconfirmationMail->hasTo('john@example.com'));

            return true;
        });
    }
}
