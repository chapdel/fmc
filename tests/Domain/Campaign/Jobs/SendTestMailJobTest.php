<?php

use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignTestJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Mails\MailcoachMail;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMail;

it('can send a test email', function () {
    Mail::fake();

    $campaign = Campaign::factory()->create();
    $campaign->contentItem->update([
        'html' => 'my html',
        'subject' => 'my subject',
    ]);

    $campaign->emailList->update(['campaign_mailer' => 'some-mailer']);

    $email = 'john@example.com';

    dispatch(new SendCampaignTestJob($campaign, $email));

    Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) use ($email) {
        expect($mail->subject)->toEqual('[Test] my subject');
        expect($mail->mailer)->toEqual('some-mailer');

        expect($mail->hasTo($email))->toBeTrue();
        expect($mail->callbacks)->toHaveCount(1);

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

    $messages = app(MailManager::class)->mailer('array')->getSymfonyTransport()->messages();

    test()->assertTrue($messages->filter(function (Symfony\Component\Mailer\SentMessage $message) {
        return $message->getOriginalMessage()->getSubject() === '[Test] This is the subject from the custom mailable.';
    })->count() > 0);
});
