<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Actions\PersonalizeHtmlAction;
use Spatie\Mailcoach\Domain\Campaign\Jobs\RetrySendingFailedSendsJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Tests\TestClasses\FailingPersonalizeHtmlForJohnAction;

it('can retry sending failed jobs sends with the correct mailer', function () {
    Mail::fake();

    $campaign = Campaign::factory()->create(['html' => 'test']);

    $campaign->emailList->update(['campaign_mailer' => 'some-mailer']);

    $john = Subscriber::createWithEmail('john@example.com')->subscribeTo($campaign->emailList);
    $jane = Subscriber::createWithEmail('jane@example.com')->subscribeTo($campaign->emailList);

    config()->set('mailcoach.campaigns.actions.personalize_html', FailingPersonalizeHtmlForJohnAction::class);
    $campaign->send();
    Artisan::call('mailcoach:send-scheduled-campaigns');
    Artisan::call('mailcoach:send-campaign-mails');

    Mail::assertSent(MailcoachMail::class, 1);
    Mail::assertSent(MailcoachMail::class, fn (MailcoachMail $mail) => $mail->hasTo($jane->email));
    Mail::assertSent(MailcoachMail::class, fn (MailcoachMail $mail) => $mail->mailer === 'some-mailer');

    $failedSends = $campaign->sends()->failed()->get();

    expect($failedSends)->toHaveCount(1);
    expect($failedSends->first()->subscriber->email)->toEqual($john->email);
    expect($failedSends->first()->failure_reason)->toEqual('Could not personalize html');

    config()->set('mailcoach.campaigns.actions.personalize_html', PersonalizeHtmlAction::class);
    dispatch(new RetrySendingFailedSendsJob($campaign));

    expect($campaign->sends()->getQuery()->pending()->count())->toBe(1);
});
