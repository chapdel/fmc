<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Models;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Mails\ConfirmSubscriberMail;
use Spatie\Mailcoach\Domain\Campaign\Mails\WelcomeMail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;

class SubscriberTest extends TestCase
{
    /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList EmailList */
    private EmailList $emailList;

    public function setUp(): void
    {
        parent::setUp();

        $this->emailList = EmailList::factory()->create();

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
    public function it_will_send_a_welcome_mail_if_the_list_has_welcome_mails()
    {
        $this->emailList->update([
            'send_welcome_mail' => true,
        ]);

        $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($this->emailList);
        $this->assertTrue($this->emailList->isSubscribed('john@example.com'));

        Mail::assertQueued(WelcomeMail::class, function (WelcomeMail $mail) use ($subscriber) {
            $this->assertEquals($subscriber->uuid, $mail->subscriber->uuid);

            return true;
        });
    }

    /** @test */
    public function it_will_only_send_a_welcome_mail_once()
    {
        $this->emailList->update([
            'send_welcome_mail' => true,
        ]);

        Subscriber::createWithEmail('john@example.com')->subscribeTo($this->emailList);
        $this->assertTrue($this->emailList->isSubscribed('john@example.com'));

        Subscriber::createWithEmail('john@example.com')->subscribeTo($this->emailList);
        $this->assertTrue($this->emailList->isSubscribed('john@example.com'));

        Mail::assertQueued(WelcomeMail::class, 1);
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
        $subscriber = Subscriber::factory()->create();
        $this->assertEquals(SubscriptionStatus::SUBSCRIBED, $subscriber->status);
        $subscriber->emailList->update(['requires_confirmation' => true]);

        $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($this->emailList);
        $this->assertEquals(SubscriptionStatus::SUBSCRIBED, $subscriber->status);

        Mail::assertNothingQueued();
    }

    /** @test */
    public function it_can_get_all_sends()
    {
        /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
        $send = SendFactory::new()->create();

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
        $subscriber = $send->subscriber;

        $sends = $subscriber->sends;

        $this->assertCount(1, $sends);

        $this->assertEquals($send->uuid, $sends->first()->uuid);
    }

    /** @test */
    public function it_can_get_all_opens()
    {
        /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
        $send = SendFactory::new()->create();
        $send->campaign->update(['track_opens' => true]);

        $send->registerOpen();

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
        $subscriber = $send->subscriber;

        $opens = $subscriber->opens;
        $this->assertCount(1, $opens);

        $this->assertEquals($send->uuid, $subscriber->opens->first()->send->uuid);
        $this->assertEquals($subscriber->uuid, $subscriber->opens->first()->subscriber->uuid);
    }

    /** @test */
    public function it_can_get_all_clicks()
    {
        /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
        $send = SendFactory::new()->create();
        $send->campaign->update(['track_clicks' => true]);

        $send->registerClick('https://example.com');
        $send->registerClick('https://another-domain.com');
        $send->registerClick('https://example.com');

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
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
