<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestCustomQueryOnlyShouldSendToJohn;
use Spatie\Mailcoach\Tests\TestClasses\TestSegmentAllSubscribers;
use Spatie\Mailcoach\Tests\TestClasses\TestSegmentQueryOnlyJohn;

class SegmentTest extends TestCase
{
    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign */
    private Campaign $campaign;

    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\EmailList */
    private EmailList $emailList;

    public function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        $this->campaign = Campaign::factory()->create();

        $this->emailList = EmailList::factory()->create();
    }

    /** @test */
    public function it_will_not_send_a_mail_if_it_is_not_subscribed_to_the_list_of_the_campaign_even_if_the_segment_selects_it()
    {
        Subscriber::factory()->create();

        $this->campaign->segment(TestSegmentAllSubscribers::class)->sendTo($this->emailList);

        Mail::assertNothingSent();
    }

    /** @test */
    public function it_can_segment_a_test_by_using_a_query()
    {
        $this->emailList->subscribe('john@example.com');
        $this->emailList->subscribe('jane@example.com');

        $this->campaign
            ->segment(TestSegmentQueryOnlyJohn::class)
            ->sendTo($this->emailList);

        Mail::assertSent(MailcoachMail::class, 1);

        Mail::assertSent(MailcoachMail::class, fn (MailcoachMail $mail) => $mail->hasTo('john@example.com'));

        Mail::assertNotSent(MailcoachMail::class, fn (MailcoachMail $mail) => $mail->hasTo('jane@example.com'));
    }

    /** @test */
    public function it_can_segment_a_test_by_using_should_send()
    {
        $this->emailList->subscribe('john@example.com');
        $this->emailList->subscribe('jane@example.com');
        $this->campaign
            ->segment(TestCustomQueryOnlyShouldSendToJohn::class)
            ->sendTo($this->emailList);
        Mail::assertSent(MailcoachMail::class, 1);
        Mail::assertSent(MailcoachMail::class, fn (MailcoachMail $mail) => $mail->hasTo('john@example.com'));
        Mail::assertNotSent(MailcoachMail::class, fn (MailcoachMail $mail) => $mail->hasTo('jane@example.com'));
        $this->assertTrue($this->campaign->fresh()->isSent());
    }
}
