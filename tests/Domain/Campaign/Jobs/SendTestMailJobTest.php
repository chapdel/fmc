<?php

use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignTestJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMail;

uses(TestCase::class);

it('can send a test email', function () {
    Mail::fake();

    $campaign = Campaign::factory()->create([
        'html' => 'my html',
        'subject' => 'my subject',
    ]);

    $campaign->emailList->update(['campaign_mailer' => 'some-mailer']);

    $email = 'john@example.com';

    dispatch(new SendCampaignTestJob($campaign, $email));

    Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) use ($email, $campaign) {
        test()->assertEquals('[Test] my subject', $mail->subject);
        test()->assertEquals('some-mailer', $mail->mailer);

        test()->assertTrue($mail->hasTo($email));
        test()->assertCount(1, $mail->callbacks);

        return true;
    });
});

test('the queue of the send test mail job can be configured', function () {
    Queue::fake();
    config()->set('mailcoach.campaigns.perform_on_queue.send_test_mail_job', 'custom-queue');

    $campaign = Campaign::factory()->create();
    dispatch(new SendCampaignTestJob($campaign, 'john@example.com'));
    Queue::assertPushedOn('custom-queue', SendCampaignTestJob::class);
});

it('can send a test email using a custom mailable', function () {
    $campaign = Campaign::factory()->create();

    $campaign->emailList->update(['campaign_mailer' => 'some-mailer']);
    $campaign->useMailable(TestMailcoachMail::class);

    $email = 'john@example.com';

    $campaign->emailList->update(['campaign_mailer' => 'array']);

    $campaign->sendTestMail($email);

    $messages = app(MailManager::class)->mailer('array')->getSwiftMailer()->getTransport()->messages();

    test()->assertTrue($messages->filter(function (\Swift_Message $message) {
        return $message->getSubject() === "[Test] This is the subject from the custom mailable.";
    })->count() > 0);
});
