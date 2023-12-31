<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Mails\CampaignSentMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

beforeEach(function () {
    test()->emailList = EmailList::factory()->create();

    test()->campaign = Campaign::factory()->create([
        'email_list_id' => test()->emailList->id,
    ]);
});

test('when a campaign is sent it will send a mail', function () {
    Mail::fake();

    test()->emailList->update([
        'report_recipients' => 'john@example.com,jane@example.com',
        'report_campaign_sent' => true,
        'transactional_mailer' => 'some-transactional-mailer',
        'campaign_mailer' => 'some-campaign-mailer',
    ]);

    config()->set('mailcoach.mailer', 'some-mailer');

    event(new CampaignSentEvent(test()->campaign));

    Mail::assertQueued(CampaignSentMail::class, function (CampaignSentMail $mail) {
        expect($mail->mailer)->toEqual('some-mailer');
        expect($mail->hasTo('john@example.com'))->toBeTrue();
        expect($mail->hasTo('jane@example.com'))->toBeTrue();

        return true;
    });
});

it('will not send a campaign sent mail if it is not enabled', function () {
    Mail::fake();

    test()->emailList->update([
        'report_campaign_sent' => false,
    ]);

    event(new CampaignSentEvent(test()->campaign));

    Mail::assertNotQueued(CampaignSentMail::class);
});

it('will not send a campaign sent mail when no destination is set', function () {
    Mail::fake();

    event(new CampaignSentEvent(test()->campaign));

    Mail::assertNotQueued(CampaignSentMail::class);
});

test('the content of the campaign sent mail is valid', function () {
    expect((new CampaignSentMail(test()->campaign))->render())->toBeString();
});
