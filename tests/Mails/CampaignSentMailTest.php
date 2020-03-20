<?php

namespace Spatie\Mailcoach\Tests\Mails;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Events\CampaignSentEvent;
use Spatie\Mailcoach\Mails\CampaignSentMail;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Tests\TestCase;

class CampaignSentMailTest extends TestCase
{
    private Campaign $campaign;

    private EmailList $emailList;

    public function setUp(): void
    {
        parent::setUp();

        $this->emailList = factory(EmailList::class)->create();

        $this->campaign = factory(Campaign::class)->create([
            'email_list_id' => $this->emailList->id,
        ]);
    }

    /** @test */
    public function when_a_campaign_is_sent_it_will_send_a_mail()
    {
        Mail::fake();

        $this->emailList->update([
            'report_recipients' => 'john@example.com,jane@example.com',
            'report_campaign_sent' => true,
            'transactional_mailer' => 'some-transactional-mailer',
            'campaign_mailer' => 'some-campaign-mailer',
        ]);

        config()->set('mailcoach.mailer', 'some-mailer');

        event(new CampaignSentEvent($this->campaign));

        Mail::assertQueued(CampaignSentMail::class, function (CampaignSentMail $mail) {
            return $mail->hasTo('john@example.com') && $mail->mailer === 'some-mailer';
        });

        Mail::assertQueued(CampaignSentMail::class, function (CampaignSentMail $mail) {
            return $mail->hasTo('jane@example.com') && $mail->mailer === 'some-mailer';
        });
    }

    /** @test */
    public function it_will_not_send_a_campaign_sent_mail_if_it_is_not_enabled()
    {
        Mail::fake();

        $this->emailList->update([
            'report_campaign_sent' => false,
        ]);

        event(new CampaignSentEvent($this->campaign));

        Mail::assertNotQueued(CampaignSentMail::class);
    }

    /** @test */
    public function it_will_not_send_a_campaign_sent_mail_when_no_destination_is_set()
    {
        Mail::fake();

        event(new CampaignSentEvent($this->campaign));

        Mail::assertNotQueued(CampaignSentMail::class);
    }

    /** @test */
    public function the_content_of_the_campaign_sent_mail_is_valid()
    {
        $this->assertIsString((new CampaignSentMail($this->campaign))->render());
    }
}
