<?php

namespace Spatie\Mailcoach\Tests\Jobs;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Campaign\Actions\PersonalizeHtmlAction;
use Spatie\Mailcoach\Domain\Campaign\Jobs\RetrySendingFailedSendsJob;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Domain\Campaign\Mails\CampaignMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\FailingPersonalizeHtmlForJohnAction;

class RetrySendingFailedSendsJobTest extends TestCase
{
    /** @test */
    public function it_can_retry_sending_failed_jobs_sends_with_the_correct_mailer()
    {
        Mail::fake();

        $campaign = Campaign::factory()->create(['html' => 'test']);

        $campaign->emailList->update(['campaign_mailer' => 'some-mailer']);

        $john = Subscriber::createWithEmail('john@example.com')->subscribeTo($campaign->emailList);
        $jane = Subscriber::createWithEmail('jane@example.com')->subscribeTo($campaign->emailList);

        config()->set('mailcoach.actions.personalize_html', FailingPersonalizeHtmlForJohnAction::class);
        dispatch(new SendCampaignJob($campaign->fresh()));

        Mail::assertSent(CampaignMail::class, 1);
        Mail::assertSent(CampaignMail::class, fn (CampaignMail $mail) => $mail->hasTo($jane->email));
        Mail::assertSent(CampaignMail::class, fn (CampaignMail $mail) => $mail->mailer === 'some-mailer');

        $failedSends = $campaign->sends()->failed()->get();

        $this->assertCount(1, $failedSends);
        $this->assertEquals($john->email, $failedSends->first()->subscriber->email);
        $this->assertEquals('Could not personalize html', $failedSends->first()->failure_reason);

        config()->set('mailcoach.actions.personalize_html', PersonalizeHtmlAction::class);
        dispatch(new RetrySendingFailedSendsJob($campaign));

        Mail::assertSent(CampaignMail::class, 2);
        Mail::assertSent(CampaignMail::class, fn (CampaignMail $mail) => $mail->hasTo($john->email));
        Mail::assertSent(CampaignMail::class, fn (CampaignMail $mail) => $mail->mailer === 'some-mailer');
    }
}
