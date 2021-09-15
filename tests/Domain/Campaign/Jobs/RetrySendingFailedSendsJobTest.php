<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Actions\PersonalizeHtmlAction;
use Spatie\Mailcoach\Domain\Campaign\Jobs\RetrySendingFailedSendsJob;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\FailingPersonalizeHtmlForJohnAction;

uses(TestCase::class);

it('can retry sending failed jobs sends with the correct mailer', function () {
    Mail::fake();

    $campaign = Campaign::factory()->create(['html' => 'test']);

    $campaign->emailList->update(['campaign_mailer' => 'some-mailer']);

    $john = Subscriber::createWithEmail('john@example.com')->subscribeTo($campaign->emailList);
    $jane = Subscriber::createWithEmail('jane@example.com')->subscribeTo($campaign->emailList);

    config()->set('mailcoach.campaigns.actions.personalize_html', FailingPersonalizeHtmlForJohnAction::class);
    dispatch(new SendCampaignJob($campaign->fresh()));

    Mail::assertSent(MailcoachMail::class, 1);
    Mail::assertSent(MailcoachMail::class, fn (MailcoachMail $mail) => $mail->hasTo($jane->email));
    Mail::assertSent(MailcoachMail::class, fn (MailcoachMail $mail) => $mail->mailer === 'some-mailer');

    $failedSends = $campaign->sends()->failed()->get();

    test()->assertCount(1, $failedSends);
    test()->assertEquals($john->email, $failedSends->first()->subscriber->email);
    test()->assertEquals('Could not personalize html', $failedSends->first()->failure_reason);

    config()->set('mailcoach.campaigns.actions.personalize_html', PersonalizeHtmlAction::class);
    dispatch(new RetrySendingFailedSendsJob($campaign));

    Mail::assertSent(MailcoachMail::class, 2);
    Mail::assertSent(MailcoachMail::class, fn (MailcoachMail $mail) => $mail->hasTo($john->email));
    Mail::assertSent(MailcoachMail::class, fn (MailcoachMail $mail) => $mail->mailer === 'some-mailer');
});
