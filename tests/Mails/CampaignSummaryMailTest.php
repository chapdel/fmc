<?php

namespace Spatie\Mailcoach\Tests\Mails;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Commands\SendCampaignSummaryMailCommand;
use Spatie\Mailcoach\Mails\CampaignSummaryMail;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class CampaignSummaryMailTest extends TestCase
{
    private Campaign $campaign;

    private EmailList $emailList;

    public function setUp(): void
    {
        parent::setUp();

        TestTime::freeze();

        $this->emailList = factory(EmailList::class)->create();

        $this->campaign = factory(Campaign::class)->create([
            'email_list_id' => $this->emailList->id,
            'sent_at' => now(),
        ]);

        $this->emailList->update([
            'report_recipients' => 'john@example.com,jane@example.com',
            'report_campaign_summary' => true,
        ]);
    }

    /** @test */
    public function after_a_day_it_will_sent_a_summary()
    {
        Mail::fake();
        config()->set('mailcoach.mailer', 'some-mailer');

        $this->artisan(SendCampaignSummaryMailCommand::class);
        Mail::assertNotQueued(CampaignSummaryMail::class);

        TestTime::addDay();
        TestTime::subSecond();

        $this->artisan(SendCampaignSummaryMailCommand::class);
        Mail::assertNotQueued(CampaignSummaryMail::class);

        TestTime::addSecond();
        $this->artisan(SendCampaignSummaryMailCommand::class);

        Mail::assertQueued(CampaignSummaryMail::class, fn (CampaignSummaryMail $mail) => $mail->mailer === 'some-mailer');
        Mail::assertQueued(CampaignSummaryMail::class, fn (CampaignSummaryMail $mail) => $mail->hasTo('john@example.com'));
        Mail::assertQueued(CampaignSummaryMail::class, fn (CampaignSummaryMail $mail) => $mail->hasTo('jane@example.com'));

        $this->assertEquals(
            now()->format('YmdHis'),
            $this->campaign->refresh()->summary_mail_sent_at->format('YmdHis')
        );
    }

    /** @test */
    public function it_will_not_send_a_summary_mail_if_it_was_already_sent()
    {
        Mail::fake();

        $this->campaign->update(['summary_mail_sent_at' => now()]);

        TestTime::addDay();
        $this->artisan(SendCampaignSummaryMailCommand::class);
        Mail::assertNotQueued(CampaignSummaryMail::class);
    }

    /** @test */
    public function it_will_not_report_a_summary_if_it_is_not_enabled_on_the_list()
    {
        Mail::fake();

        $this->emailList->update([
            'report_campaign_summary' => false,
        ]);

        TestTime::addDay();

        $this->artisan(SendCampaignSummaryMailCommand::class);

        Mail::assertNotQueued(CampaignSummaryMail::class);
    }

    /** @test */
    public function the_content_of_the_campaign_summary_mail_is_valid()
    {
        $this->assertIsString((new CampaignSummaryMail($this->campaign))->render());
    }
}
