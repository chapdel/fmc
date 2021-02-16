<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Mails;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Commands\SendCampaignSummaryMailCommand;
use Spatie\Mailcoach\Domain\Campaign\Mails\CampaignSummaryMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class CampaignSummaryMailTest extends TestCase
{
    protected Campaign $campaign;

    protected EmailList $emailList;

    public function setUp(): void
    {
        parent::setUp();

        TestTime::freeze();

        $this->emailList = EmailList::factory()->create();

        $this->campaign = Campaign::factory()->create([
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

    /** @test */
    public function the_mail_contains_correct_statistics()
    {
        $this->campaign->update([
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

        $mail = (new CampaignSummaryMail($this->campaign));
        $html = $mail->render();

        $this->assertStringContainsString('8018', $html);
        $this->assertStringContainsString('5516', $html);
        $this->assertStringContainsString('3192', $html);
        $this->assertStringContainsString('39.81', $html);
        $this->assertStringContainsString('2972', $html);
        $this->assertStringContainsString('948', $html);
        $this->assertStringContainsString('11.82', $html);
        $this->assertStringContainsString('15', $html);
        $this->assertStringContainsString('0.19', $html);
    }
}
