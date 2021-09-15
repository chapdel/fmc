<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailJob;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMail;
use Spatie\TestTime\TestTime;

uses(TestCase::class);

beforeEach(function () {
    Mail::fake();
});

it('can send a mail with the correct mailer', function () {
    $pendingSend = SendFactory::new()->create();
    $pendingSend->campaign->emailList->update(['campaign_mailer' => 'some-mailer']);

    dispatch(new SendCampaignMailJob($pendingSend));

    Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) use ($pendingSend) {
        test()->assertEquals('some-mailer', $mail->mailer);
        test()->assertEquals($pendingSend->campaign->subject, $mail->subject);
        test()->assertTrue($mail->hasTo($pendingSend->subscriber->email));
        test()->assertCount(1, $mail->callbacks);

        return true;
    });
});

it('will rate limit', function () {
    TestTime::freeze()->addDay();

    config()->set('mailcoach.campaigns.throttling.allowed_number_of_jobs_in_timespan', 1);

    $pendingSend = SendFactory::new()->create();
    $pendingSend2 = SendFactory::new()->create();
    $pendingSend3 = SendFactory::new()->create();

    dispatch(new SendCampaignMailJob($pendingSend));
    dispatch(new SendCampaignMailJob($pendingSend2));
    dispatch(new SendCampaignMailJob($pendingSend3));

    Mail::assertSent(MailcoachMail::class, 1);
});

it('will not resend a mail that has already been sent', function () {
    $pendingSend = SendFactory::new()->create();

    test()->assertFalse($pendingSend->wasAlreadySent());

    dispatch(new SendCampaignMailJob($pendingSend));

    test()->assertTrue($pendingSend->refresh()->wasAlreadySent());
    Mail::assertSent(MailcoachMail::class, 1);

    dispatch(new SendCampaignMailJob($pendingSend));
    Mail::assertSent(MailcoachMail::class, 1);
});

test('the queue of the send mail job can be configured', function () {
    Queue::fake();
    config()->set('mailcoach.campaigns.perform_on_queue.send_mail_job', 'custom-queue');

    $pendingSend = SendFactory::new()->create();
    dispatch(new SendCampaignMailJob($pendingSend));
    Queue::assertPushedOn('custom-queue', SendCampaignMailJob::class);
});

it('can use a custom mailable', function () {
    $pendingSend = SendFactory::new()->create();

    $pendingSend->campaign->useMailable(TestMailcoachMail::class);

    dispatch(new SendCampaignMailJob($pendingSend));

    Mail::assertSent(MailcoachMail::class, 1);
});
