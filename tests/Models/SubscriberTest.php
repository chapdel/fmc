<?php

namespace Spatie\Mailcoach\Tests\Models;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Mails\ConfirmSubscriberMail;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;

class SubscriberTest extends TestCase
{
    /** @var \Spatie\Mailcoach\Models\EmailList EmailList */
    private EmailList $emailList;

    public function setUp(): void
    {
        parent::setUp();

        $this->emailList = factory(EmailList::class)->create();

        Mail::fake();
    }

    /** @test */
    public function it_will_only_subscribe_a_subscriber_once()
    {
        $this->assertFalse($this->emailList->isSubscribed('john@example.com'));

        Subscriber::createWithEmail('john@example.com')->subscribeTo($this->emailList);
        $this->assertTrue($this->emailList->isSubscribed('john@example.com'));

        Subscriber::createWithEmail('john@example.com')->subscribeTo($this->emailList);
        $this->assertTrue($this->emailList->isSubscribed('john@example.com'));

        $this->assertEquals(1, Subscriber::count());
    }

    /** @test */
    public function it_can_resubscribe_someone()
    {
        $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($this->emailList);
        $this->assertTrue($this->emailList->isSubscribed('john@example.com'));

        $subscriber->unsubscribe();
        $this->assertFalse($this->emailList->isSubscribed('john@example.com'));

        Subscriber::createWithEmail('john@example.com')->subscribeTo($this->emailList);
        $this->assertTrue($this->emailList->isSubscribed('john@example.com'));
    }

    /** @test */
    public function it_will_send_a_confirmation_mail_if_the_list_requires_double_optin()
    {
        $this->emailList->update([
            'requires_confirmation' => true,
        ]);

        $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($this->emailList);
        $this->assertFalse($this->emailList->isSubscribed('john@example.com'));

        Mail::assertQueued(ConfirmSubscriberMail::class, function (ConfirmSubscriberMail $mail) use ($subscriber) {
            $this->assertEquals($subscriber->uuid, $mail->subscriber->uuid);

            return true;
        });
    }

    /** @test */
    public function it_can_immediately_subscribe_someone_and_not_send_a_mail_even_with_double_opt_in_enabled()
    {
        $this->emailList->update([
            'requires_confirmation' => true,
        ]);

        $subscriber = Subscriber::createWithEmail('john@example.com')
            ->skipConfirmation()
            ->subscribeTo($this->emailList);

        $this->assertEquals(SubscriptionStatus::SUBSCRIBED, $subscriber->status);
        $this->assertTrue($this->emailList->isSubscribed('john@example.com'));

        Mail::assertNotQueued(ConfirmSubscriberMail::class);
    }

    /** @test */
    public function no_email_will_be_sent_when_adding_someone_that_was_already_subscribed()
    {
        $subscriber = factory(Subscriber::class)->create();
        $this->assertEquals(SubscriptionStatus::SUBSCRIBED, $subscriber->status);
        $subscriber->emailList->update(['requires_confirmation' => true]);

        $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($this->emailList);
        $this->assertEquals(SubscriptionStatus::SUBSCRIBED, $subscriber->status);

        Mail::assertNothingQueued();
    }

    /** @test */
    public function it_can_get_all_sends()
    {
        /** @var \Spatie\Mailcoach\Models\Send $send */
        $send = factory(Send::class)->create();

        /** @var \Spatie\Mailcoach\Models\Subscriber $subscriber */
        $subscriber = $send->subscriber;

        $sends = $subscriber->sends;

        $this->assertCount(1, $sends);

        $this->assertEquals($send->uuid, $sends->first()->uuid);
    }

    /** @test */
    public function it_can_get_all_opens()
    {
        /** @var \Spatie\Mailcoach\Models\Send $send */
        $send = factory(Send::class)->create();
        $send->campaign->update(['track_opens' => true]);

        $send->registerOpen();

        /** @var \Spatie\Mailcoach\Models\Subscriber $subscriber */
        $subscriber = $send->subscriber;

        $opens = $subscriber->opens;
        $this->assertCount(1, $opens);

        $this->assertEquals($send->uuid, $subscriber->opens->first()->send->uuid);
        $this->assertEquals($subscriber->uuid, $subscriber->opens->first()->subscriber->uuid);
    }

    /** @test */
    public function it_can_get_all_clicks()
    {
        /** @var \Spatie\Mailcoach\Models\Send $send */
        $send = factory(Send::class)->create();
        $send->campaign->update(['track_clicks' => true]);

        $send->registerClick('https://example.com');
        $send->registerClick('https://another-domain.com');
        $send->registerClick('https://example.com');

        /** @var \Spatie\Mailcoach\Models\Subscriber $subscriber */
        $subscriber = $send->subscriber;

        $clicks = $subscriber->clicks;
        $this->assertCount(3, $clicks);

        $uniqueClicks = $subscriber->uniqueClicks;
        $this->assertCount(2, $uniqueClicks);

        $this->assertEquals(
            ['https://example.com','https://another-domain.com'],
            $uniqueClicks->pluck('link.url')->toArray()
        );
    }
}
