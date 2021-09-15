<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Commands\SendCampaignSummaryMailCommand;
use Spatie\Mailcoach\Domain\Campaign\Mails\CampaignSummaryMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;



beforeEach(function () {
    TestTime::freeze();

    test()->emailList = EmailList::factory()->create();

    test()->campaign = Campaign::factory()->create([
        'email_list_id' => test()->emailList->id,
        'sent_at' => now(),
    ]);

    test()->emailList->update([
        'report_recipients' => 'john@example.com,jane@example.com',
        'report_campaign_summary' => true,
    ]);
});

test('after a day it will sent a summary', function () {
    Mail::fake();
    config()->set('mailcoach.mailer', 'some-mailer');

    test()->artisan(SendCampaignSummaryMailCommand::class);
    Mail::assertNotQueued(CampaignSummaryMail::class);

    TestTime::addDay();
    TestTime::subSecond();

    test()->artisan(SendCampaignSummaryMailCommand::class);
    Mail::assertNotQueued(CampaignSummaryMail::class);

    TestTime::addSecond();
    test()->artisan(SendCampaignSummaryMailCommand::class);

    Mail::assertQueued(CampaignSummaryMail::class, fn (CampaignSummaryMail $mail) => $mail->mailer === 'some-mailer');
    Mail::assertQueued(CampaignSummaryMail::class, fn (CampaignSummaryMail $mail) => $mail->hasTo('john@example.com'));
    Mail::assertQueued(CampaignSummaryMail::class, fn (CampaignSummaryMail $mail) => $mail->hasTo('jane@example.com'));

    test()->assertEquals(
        now()->format('YmdHis'),
        test()->campaign->refresh()->summary_mail_sent_at->format('YmdHis')
    );
});

it('will not send a summary mail if it was already sent', function () {
    Mail::fake();

    test()->campaign->update(['summary_mail_sent_at' => now()]);

    TestTime::addDay();
    test()->artisan(SendCampaignSummaryMailCommand::class);
    Mail::assertNotQueued(CampaignSummaryMail::class);
});

it('will not report a summary if it is not enabled on the list', function () {
    Mail::fake();

    test()->emailList->update([
        'report_campaign_summary' => false,
    ]);

    TestTime::addDay();

    test()->artisan(SendCampaignSummaryMailCommand::class);

    Mail::assertNotQueued(CampaignSummaryMail::class);
});

test('the content of the campaign summary mail is valid', function () {
    expect((new CampaignSummaryMail(test()->campaign))->render())->toBeString();
});

test('the mail contains correct statistics', function () {
    test()->campaign->update([
        'sent_to_number_of_subscribers' => 8018,
        'open_count' => 5516,
        'unique_open_count' => 3192,
        'open_rate' => 3981,
        'click_count' => 2972,
        'unique_click_count' => 948,
        'click_rate' => 1182,
        'unsubscribe_count' => 15,
        'unsubscribe_rate' => 19,
        'track_clicks' => true,
        'track_opens' => true,
        ]);

    $mail = (new CampaignSummaryMail(test()->campaign));
    $html = $mail->render();

    expect($html)->toContain('8018');
    expect($html)->toContain('5516');
    expect($html)->toContain('3192');
    expect($html)->toContain('39.81');
    expect($html)->toContain('2972');
    expect($html)->toContain('948');
    expect($html)->toContain('11.82');
    expect($html)->toContain('15');
    expect($html)->toContain('0.19');
});
