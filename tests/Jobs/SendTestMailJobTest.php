<?php

namespace Spatie\Mailcoach\Tests\Jobs;

use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Jobs\SendTestMailJob;
use Spatie\Mailcoach\Mails\CampaignMail;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestCampaignMail;

class SendTestMailJobTest extends TestCase
{
    /** @test */
    public function it_can_send_a_test_email()
    {
        Mail::fake();

        $campaign = factory(Campaign::class)->create([
            'html' => 'my html',
            'subject' => 'my subject',
        ]);

        $campaign->emailList->update(['campaign_mailer' => 'some-mailer']);

        $email = 'john@example.com';

        dispatch(new SendTestMailJob($campaign, $email));

        Mail::assertSent(CampaignMail::class, function (CampaignMail $mail) use ($email, $campaign) {
            $this->assertEquals('my subject', $mail->subject);
            $this->assertEquals('some-mailer', $mail->mailer);

            $this->assertTrue($mail->hasTo($email));
            $this->assertCount(1, $mail->callbacks);

            return true;
        });
    }

    /** @test */
    public function the_queue_of_the_send_test_mail_job_can_be_configured()
    {
        Queue::fake();
        config()->set('mailcoach.perform_on_queue.send_test_mail_job', 'custom-queue');

        $campaign = factory(Campaign::class)->create();
        dispatch(new SendTestMailJob($campaign, 'john@example.com'));
        Queue::assertPushedOn('custom-queue', SendTestMailJob::class);
    }

    /** @test */
    public function it_can_send_a_test_email_using_a_custom_mailable()
    {
        $campaign = factory(Campaign::class)->create();

        $campaign->emailList->update(['campaign_mailer' => 'some-mailer']);
        $campaign->useMailable(TestCampaignMail::class);

        $email = 'john@example.com';

        $campaign->emailList->update(['campaign_mailer' => 'array']);

        dispatch(new SendTestMailJob($campaign, $email));

        $messages = app(MailManager::class)->mailer('array')->getSwiftMailer()->getTransport()->messages();

        $this->assertTrue($messages->filter(function (\Swift_Message $message) {
            return $message->getSubject() === "This is the subject from the custom mailable.";
        })->count() > 0);
    }
}
