<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Jobs;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailJob;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMail;
use Spatie\TestTime\TestTime;

class SendMailJobTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Mail::fake();
    }

    /** @test */
    public function it_can_send_a_mail_with_the_correct_mailer()
    {
        $pendingSend = SendFactory::new()->create();
        $pendingSend->campaign->emailList->update(['campaign_mailer' => 'some-mailer']);

        dispatch(new SendCampaignMailJob($pendingSend));

        Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) use ($pendingSend) {
            $this->assertEquals('some-mailer', $mail->mailer);
            $this->assertEquals($pendingSend->campaign->subject, $mail->subject);
            $this->assertTrue($mail->hasTo($pendingSend->subscriber->email));
            $this->assertCount(1, $mail->callbacks);

            return true;
        });
    }

    /** @test */
    public function it_will_rate_limit()
    {
        TestTime::freeze();

        config()->set('mailcoach.campaigns.throttling.allowed_number_of_jobs_in_timespan', 1);

        $pendingSend = SendFactory::new()->create();
        $pendingSend2 = SendFactory::new()->create();
        $pendingSend3 = SendFactory::new()->create();

        dispatch(new SendCampaignMailJob($pendingSend));
        dispatch(new SendCampaignMailJob($pendingSend2));
        dispatch(new SendCampaignMailJob($pendingSend3));

        Mail::assertSent(MailcoachMail::class, 1);
    }

    /** @test */
    public function it_will_not_resend_a_mail_that_has_already_been_sent()
    {
        $pendingSend = SendFactory::new()->create();

        $this->assertFalse($pendingSend->wasAlreadySent());

        dispatch(new SendCampaignMailJob($pendingSend));

        $this->assertTrue($pendingSend->refresh()->wasAlreadySent());
        Mail::assertSent(MailcoachMail::class, 1);

        dispatch(new SendCampaignMailJob($pendingSend));
        Mail::assertSent(MailcoachMail::class, 1);
    }

    /** @test */
    public function the_queue_of_the_send_mail_job_can_be_configured()
    {
        Queue::fake();
        config()->set('mailcoach.campaigns.perform_on_queue.send_mail_job', 'custom-queue');

        $pendingSend = SendFactory::new()->create();
        dispatch(new SendCampaignMailJob($pendingSend));
        Queue::assertPushedOn('custom-queue', SendCampaignMailJob::class);
    }

    /** @test */
    public function it_can_use_a_custom_mailable()
    {
        $pendingSend = SendFactory::new()->create();

        $pendingSend->campaign->useMailable(TestMailcoachMail::class);

        dispatch(new SendCampaignMailJob($pendingSend));

        Mail::assertSent(MailcoachMail::class, 1);
    }
}
