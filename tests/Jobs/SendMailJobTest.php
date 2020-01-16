<?php

namespace Spatie\Mailcoach\Tests\Jobs;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Jobs\SendMailJob;
use Spatie\Mailcoach\Mails\CampaignMail;
use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestCampaignMail;

class SendMailJobTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Mail::fake();
    }

    /** @test */
    public function it_can_send_a_mail()
    {
        $pendingSend = factory(Send::class)->create();

        dispatch(new SendMailJob($pendingSend));

        Mail::assertSent(CampaignMail::class, function (CampaignMail $mail) use ($pendingSend) {
            $this->assertEquals($pendingSend->campaign->subject, $mail->subject);
            $this->assertTrue($mail->hasTo($pendingSend->subscriber->email));

            return true;
        });
    }

    /** @test */
    public function it_will_not_resend_a_mail_that_has_already_been_sent()
    {
        $pendingSend = factory(Send::class)->create();

        $this->assertFalse($pendingSend->wasAlreadySent());

        dispatch(new SendMailJob($pendingSend));

        $this->assertTrue($pendingSend->refresh()->wasAlreadySent());
        Mail::assertSent(CampaignMail::class, 1);

        dispatch(new SendMailJob($pendingSend));
        Mail::assertSent(CampaignMail::class, 1);
    }

    /** @test */
    public function the_queue_of_the_send_mail_job_can_be_configured()
    {
        Queue::fake();
        config()->set('mailcoach.perform_on_queue.send_mail_job', 'custom-queue');

        $pendingSend = factory(Send::class)->create();
        dispatch(new SendMailJob($pendingSend));
        Queue::assertPushedOn('custom-queue', SendMailJob::class);
    }

    /** @test */
    public function it_can_use_a_custom_mailable()
    {
        $pendingSend = factory(Send::class)->create();

        $pendingSend->campaign->useMailable(TestCampaignMail::class);

        dispatch(new SendMailJob($pendingSend));

        Mail::assertSent(TestCampaignMail::class, 1);
    }
}
