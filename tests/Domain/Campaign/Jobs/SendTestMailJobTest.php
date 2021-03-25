<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Jobs;

use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignTestJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMail;

class SendTestMailJobTest extends TestCase
{
    /** @test */
    public function it_can_send_a_test_email()
    {
        Mail::fake();

        $campaign = Campaign::factory()->create([
            'html' => 'my html',
            'subject' => 'my subject',
        ]);

        $campaign->emailList->update(['campaign_mailer' => 'some-mailer']);

        $email = 'john@example.com';

        dispatch(new SendCampaignTestJob($campaign, $email));

        Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) use ($email, $campaign) {
            $this->assertEquals('[Test] my subject', $mail->subject);
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
        config()->set('mailcoach.campaigns.perform_on_queue.send_test_mail_job', 'custom-queue');

        $campaign = Campaign::factory()->create();
        dispatch(new SendCampaignTestJob($campaign, 'john@example.com'));
        Queue::assertPushedOn('custom-queue', SendCampaignTestJob::class);
    }

    /** @test */
    public function it_can_send_a_test_email_using_a_custom_mailable()
    {
        $campaign = Campaign::factory()->create();

        $campaign->emailList->update(['campaign_mailer' => 'some-mailer']);
        $campaign->useMailable(TestMailcoachMail::class);

        $email = 'john@example.com';

        $campaign->emailList->update(['campaign_mailer' => 'array']);

        $campaign->sendTestMail($email);

        $messages = app(MailManager::class)->mailer('array')->getSwiftMailer()->getTransport()->messages();

        $this->assertTrue($messages->filter(function (\Swift_Message $message) {
            return $message->getSubject() === "[Test] This is the subject from the custom mailable.";
        })->count() > 0);
    }
}