<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Jobs;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Database\Factories\CampaignSendFactory;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendMailJob;
use Spatie\Mailcoach\Domain\Campaign\Mails\CampaignMail;
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
    public function it_can_send_a_mail_with_the_correct_mailer()
    {
        $pendingSend = CampaignSendFactory::new()->create();
        $pendingSend->campaign->emailList->update(['campaign_mailer' => 'some-mailer']);

        dispatch(new SendMailJob($pendingSend));

        Mail::assertSent(CampaignMail::class, function (CampaignMail $mail) use ($pendingSend) {
            $this->assertEquals('some-mailer', $mail->mailer);
            $this->assertEquals($pendingSend->campaign->subject, $mail->subject);
            $this->assertTrue($mail->hasTo($pendingSend->subscriber->email));
            $this->assertCount(1, $mail->callbacks);

            return true;
        });
    }

    /** @test */
    public function it_will_not_resend_a_mail_that_has_already_been_sent()
    {
        $pendingSend = CampaignSendFactory::new()->create();

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

        $pendingSend = CampaignSendFactory::new()->create();
        dispatch(new SendMailJob($pendingSend));
        Queue::assertPushedOn('custom-queue', SendMailJob::class);
    }

    /** @test */
    public function it_can_use_a_custom_mailable()
    {
        $pendingSend = CampaignSendFactory::new()->create();

        $pendingSend->campaign->useMailable(TestCampaignMail::class);

        dispatch(new SendMailJob($pendingSend));

        Mail::assertSent(CampaignMail::class, 1);
    }
}
