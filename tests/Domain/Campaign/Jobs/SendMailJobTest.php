<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailJob;
use Spatie\Mailcoach\Domain\Content\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMail;

beforeEach(function () {
    Mail::fake();
});

it('can send a mail with the correct mailer', function () {
    $pendingSend = SendFactory::new()->create();
    $pendingSend->subscriber->update(['email_list_id' => $pendingSend->contentItem->model->emailList->id]);
    $pendingSend->contentItem->model->emailList->update(['campaign_mailer' => 'some-mailer']);

    dispatch(new SendCampaignMailJob($pendingSend));

    Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) use ($pendingSend) {
        expect($mail->mailer)->toEqual('some-mailer');
        expect($mail->subject)->toEqual($pendingSend->contentItem->subject);
        expect($mail->hasTo($pendingSend->subscriber->email))->toBeTrue();
        expect($mail->callbacks)->toHaveCount(1);

        return true;
    });
});

it('will not resend a mail that has already been sent', function () {
    $pendingSend = Send::factory()->create();
    $pendingSend->subscriber->update(['email_list_id' => $pendingSend->contentItem->model->emailList->id]);

    expect($pendingSend->wasAlreadySent())->toBeFalse();

    dispatch(new SendCampaignMailJob($pendingSend));

    expect($pendingSend->refresh()->wasAlreadySent())->toBeTrue();
    Mail::assertSent(MailcoachMail::class, 1);

    dispatch(new SendCampaignMailJob($pendingSend));
    Mail::assertSent(MailcoachMail::class, 1);
});

test('the queue of the send mail job can be configured', function () {
    Queue::fake();
    config()->set('mailcoach.campaigns.perform_on_queue.send_mail_job', 'custom-queue');

    $pendingSend = SendFactory::new()->create();
    $pendingSend->subscriber->update(['email_list_id' => $pendingSend->contentItem->model->emailList->id]);
    dispatch(new SendCampaignMailJob($pendingSend));
    Queue::assertPushedOn('custom-queue', SendCampaignMailJob::class);
});

it('can use a custom mailable', function () {
    $pendingSend = SendFactory::new()->create();
    $pendingSend->subscriber->update(['email_list_id' => $pendingSend->contentItem->model->emailList->id]);

    $pendingSend->contentItem->model->useMailable(TestMailcoachMail::class);

    dispatch(new SendCampaignMailJob($pendingSend));

    Mail::assertSent(MailcoachMail::class, 1);
});
