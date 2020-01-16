<?php

namespace Spatie\Mailcoach\Tests\Jobs;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Actions\Campaigns\PersonalizeHtmlAction;
use Spatie\Mailcoach\Jobs\RetrySendingFailedSendsJob;
use Spatie\Mailcoach\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Mails\CampaignMail;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\FailingPersonalizeHtmlForJohnAction;

class RetrySendingFailedSendsJobTest extends TestCase
{
    /** @test */
    public function it_can_retry_sending_failed_jobs_sends()
    {
        Mail::fake();

        $campaign = factory(Campaign::class)->create(['html' => 'test']);

        $john = Subscriber::createWithEmail('john@example.com')->subscribeTo($campaign->emailList);
        $jane = Subscriber::createWithEmail('jane@example.com')->subscribeTo($campaign->emailList);

        config()->set('mailcoach.actions.personalize_html', FailingPersonalizeHtmlForJohnAction::class);
        dispatch(new SendCampaignJob($campaign));

        Mail::assertSent(CampaignMail::class, 1);
        Mail::assertSent(CampaignMail::class, fn (CampaignMail $mail) => $mail->hasTo($jane->email));

        $failedSends = $campaign->sends()->failed()->get();

        $this->assertCount(1, $failedSends);
        $this->assertEquals($john->email, $failedSends->first()->subscriber->email);
        $this->assertEquals('Could not personalize html', $failedSends->first()->failure_reason);

        config()->set('mailcoach.actions.personalize_html', PersonalizeHtmlAction::class);
        dispatch(new RetrySendingFailedSendsJob($campaign));

        Mail::assertSent(CampaignMail::class, 2);
        Mail::assertSent(CampaignMail::class, fn (CampaignMail $mail) => $mail->hasTo($john->email));
    }
}
