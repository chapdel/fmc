<?php

namespace Spatie\Mailcoach\Tests\Models;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Enums\CampaignStatus;
use Spatie\Mailcoach\Exceptions\CouldNotSendCampaign;
use Spatie\Mailcoach\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Jobs\SendTestMailJob;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestCampaignMail;
use Spatie\Mailcoach\Tests\TestClasses\TestCustomQueryOnlyShouldSendToJohn;
use Spatie\Snapshots\MatchesSnapshots;
use Spatie\TestTime\TestTime;

class CampaignTest extends TestCase
{
    use MatchesSnapshots;

    /** @var \Spatie\Mailcoach\Models\Campaign */
    private Campaign $campaign;

    public function setUp(): void
    {
        parent::setUp();

        Queue::fake();

        $this->campaign = Campaign::create()->refresh();
    }

    /** @test */
    public function the_default_status_is_draft()
    {
        $this->assertEquals(CampaignStatus::DRAFT, $this->campaign->status);
    }

    /** @test */
    public function it_can_set_a_from_email()
    {
        $this->campaign->from('sender@example.com');

        $this->assertEquals('sender@example.com', $this->campaign->from_email);
    }

    /** @test */
    public function it_can_set_both_a_from_email_and_a_from_name()
    {
        $this->campaign->from('sender@example.com', 'Sender name');

        $this->assertEquals('sender@example.com', $this->campaign->from_email);
        $this->assertEquals('Sender name', $this->campaign->from_name);
    }

    /** @test */
    public function it_can_be_marked_to_track_opens()
    {
        $this->assertFalse($this->campaign->track_opens);

        $this->campaign->trackOpens();

        $this->assertTrue($this->campaign->refresh()->track_opens);
    }

    /** @test */
    public function it_can_be_marked_to_track_clicks()
    {
        $this->assertFalse($this->campaign->track_clicks);

        $this->campaign->trackClicks();

        $this->assertTrue($this->campaign->refresh()->track_clicks);
    }

    /** @test */
    public function it_can_add_a_subject()
    {
        $this->assertNull($this->campaign->subject);

        $this->campaign->subject('hello');

        $this->assertEquals('hello', $this->campaign->refresh()->subject);
    }

    /** @test */
    public function it_can_add_a_list()
    {
        $list = factory(EmailList::class)->create();

        $this->campaign->to($list);

        $this->assertEquals($list->id, $this->campaign->refresh()->email_list_id);
    }

    /** @test */
    public function it_can_be_sent()
    {
        $list = factory(EmailList::class)->create();

        $campaign = Campaign::create()
            ->from('test@example.com')
            ->subject('test')
            ->content('my content')
            ->to($list)
            ->send();

        Queue::assertPushed(SendCampaignJob::class, function (SendCampaignJob $job) use ($campaign) {
            $this->assertEquals($campaign->id, $job->campaign->id);

            return true;
        });
    }

    /** @test */
    public function it_has_a_shorthand_to_set_the_list_and_send_it_in_one_go()
    {
        $list = factory(EmailList::class)->create();

        $campaign = Campaign::create()
            ->from('test@example.com')
            ->content('my content')
            ->subject('test')
            ->sendTo($list);

        $this->assertEquals($list->id, $campaign->refresh()->email_list_id);

        Queue::assertPushed(SendCampaignJob::class, function (SendCampaignJob $job) use ($campaign) {
            $this->assertEquals($campaign->id, $job->campaign->id);

            return true;
        });
    }

    /** @test */
    public function a_mailable_can_be_set()
    {
        /** @var \Spatie\Mailcoach\Models\Campaign $campaign */
        $campaign = Campaign::create()->useMailable(TestCampaignMail::class);

        $this->assertEquals(TestCampaignMail::class, $campaign->mailable_class);
    }

    /** @test */
    public function it_will_throw_an_exception_when_use_an_invalid_mailable_class()
    {
        $this->expectException(CouldNotSendCampaign::class);

        Campaign::create()->useMailable(static::class);
    }

    /** @test */
    public function a_segment_can_be_set()
    {
        $campaign = Campaign::create()
            ->segment(TestCustomQueryOnlyShouldSendToJohn::class);

        $this->assertEquals(TestCustomQueryOnlyShouldSendToJohn::class, $campaign->segment_class);
    }

    /** @test */
    public function it_will_throw_an_exception_when_use_an_invalid_segment_class()
    {
        $this->expectException(CouldNotSendCampaign::class);

        Campaign::create()->segment(static::class);
    }

    /** @test */
    public function html_and_content_are_not_required_when_sending_a_mailable()
    {
        Bus::fake();

        $list = factory(EmailList::class)->create();

        Campaign::create()
            ->from('test@example.com')
            ->content('my content')
            ->subject('test')
            ->sendTo($list);

        Bus::assertDispatched(SendCampaignJob::class);
    }

    /** @test */
    public function it_can_use_the_default_from_email_and_name_set_on_the_email_list()
    {
        Bus::fake();

        $list = factory(EmailList::class)->create([
            'default_from_email' => 'defaultEmailList@example.com',
            'default_from_name' => 'List name',
        ]);

        Campaign::create()
            ->content('my content')
            ->subject('test')
            ->sendTo($list);

        Bus::assertDispatched(SendCampaignJob::class, function (SendCampaignJob $job) {
            $this->assertEquals('defaultEmailList@example.com', $job->campaign->from_email);
            $this->assertEquals('List name', $job->campaign->from_name);

            return true;
        });
    }

    /** @test */
    public function it_will_prefer_the_email_and_from_name_from_the_campaign_over_the_defaults_set_on_the_email_list()
    {
        Bus::fake();

        $list = factory(EmailList::class)->create([
            'default_from_email' => 'defaultEmailList@example.com',
            'default_from_name' => 'List name',
        ]);

        Campaign::create()
            ->content('my content')
            ->subject('test')
            ->from('campaign@example.com', 'campaign from name')
            ->sendTo($list);

        Bus::assertDispatched(SendCampaignJob::class, function (SendCampaignJob $job) {
            $this->assertEquals('campaign@example.com', $job->campaign->from_email);
            $this->assertEquals('campaign from name', $job->campaign->from_name);

            return true;
        });
    }

    /** @test */
    public function it_has_a_scope_that_can_get_campaigns_sent_in_a_certain_period()
    {
        $sentAt1430 = CampaignFactory::createSentAt('2019-01-01 14:30:00');
        $sentAt1530 = CampaignFactory::createSentAt('2019-01-01 15:30:00');
        $sentAt1630 = CampaignFactory::createSentAt('2019-01-01 16:30:00');
        $sentAt1730 = CampaignFactory::createSentAt('2019-01-01 17:30:00');

        $campaigns = Campaign::sentBetween(
            Carbon::createFromFormat('Y-m-d H:i:s', '2019-01-01 13:30:00'),
            Carbon::createFromFormat('Y-m-d H:i:s', '2019-01-01 17:30:00'),
        )->get();

        $this->assertEquals(
            [$sentAt1430->id, $sentAt1530->id, $sentAt1630->id],
            $campaigns->pluck('id')->values()->toArray(),
        );
    }

    /** @test */
    public function it_can_send_out_a_test_email()
    {
        Bus::fake();

        $email = 'john@example.com';

        $this->campaign->sendTestMail($email);

        Bus::assertDispatched(SendTestMailJob::class, function (SendTestMailJob $job) use ($email) {
            $this->assertEquals($this->campaign->id, $job->campaign->id);
            $this->assertEquals($email, $job->email);

            return true;
        });
    }

    /** @test */
    public function it_can_send_out_multiple_test_emails_at_once()
    {
        Bus::fake();

        $this->campaign->sendTestMail(['john@example.com', 'paul@example.com']);

        Bus::assertDispatched(SendTestMailJob::class, fn (SendTestMailJob $job) => $job->email === 'john@example.com');

        Bus::assertDispatched(SendTestMailJob::class, fn (SendTestMailJob $job) => $job->email === 'paul@example.com');
    }

    /** @test */
    public function it_can_dispatch_a_job_to_recalculate_statistics()
    {
        Bus::fake();

        $this->campaign->dispatchCalculateStatistics();

        Bus::assertDispatched(CalculateStatisticsJob::class, 1);
    }

    /** @test */
    public function it_will_not_dispatch_the_recalculation_job_twice()
    {
        Bus::fake();

        $this->campaign->dispatchCalculateStatistics();
        $this->campaign->dispatchCalculateStatistics();

        Bus::assertDispatched(CalculateStatisticsJob::class, 1);
    }

    /** @test */
    public function it_can_dispatch_the_recalculation_job_again_after_the_previous_job_has_run()
    {
        Bus::fake();

        $this->campaign->dispatchCalculateStatistics();

        (new CalculateStatisticsJob($this->campaign))->handle();

        $this->campaign->dispatchCalculateStatistics();

        Bus::assertDispatched(CalculateStatisticsJob::class, 2);
    }

    /** @test */
    public function it_has_scopes_to_get_campaigns_in_various_states()
    {
        Campaign::truncate();

        $draftCampaign = factory(Campaign::class)->create([
            'status' => CampaignStatus::DRAFT,
        ]);

        $scheduledInThePastCampaign = factory(Campaign::class)->create([
            'status' => CampaignStatus::DRAFT,
            'scheduled_at' => now()->subSecond()
        ]);

        $scheduledNowCampaign = factory(Campaign::class)->create([
            'status' => CampaignStatus::DRAFT,
            'scheduled_at' => now()
        ]);

        $scheduledInFutureCampaign = factory(Campaign::class)->create([
            'status' => CampaignStatus::DRAFT,
            'scheduled_at' => now()->addSecond()
        ]);

        $sendingCampaign = factory(Campaign::class)->create([
            'status' => CampaignStatus::SENDING,
        ]);

        $sentCampaign = factory(Campaign::class)->create([
            'status' => CampaignStatus::SENT,
        ]);

        $this->assertModels([
            $draftCampaign
        ], Campaign::draft()->get());

        $this->assertModels([
            $scheduledInThePastCampaign,
            $scheduledNowCampaign,
            $scheduledInFutureCampaign,
        ], Campaign::scheduled()->get());

        $this->assertModels([
            $scheduledInThePastCampaign,
            $scheduledNowCampaign,
        ], Campaign::shouldBeSentNow()->get());

        $this->assertModels([
            $sendingCampaign,
            $sentCampaign,
        ], Campaign::sendingOrSent()->get());
    }

    /** @test */
    public function it_can_send_determine_if_it_has_troubles_sending_out_mails()
    {
        TestTime::freeze();

        $this->assertFalse($this->campaign->hasTroublesSendingOutMails());

        $this->campaign->update([
            'status' => CampaignStatus::SENDING,
            'last_modified_at' => now(),
        ]);

        factory(Send::class)->create([
            'campaign_id' => $this->campaign->id,
            'sent_at' => now(),
        ]);

        $send = factory(Send::class)->create([
            'campaign_id' => $this->campaign->id,
            'sent_at' => null,
        ]);

        $this->assertFalse($this->campaign->hasTroublesSendingOutMails());

        TestTime::addHour();
        $this->assertTrue($this->campaign->hasTroublesSendingOutMails());

        $send->update(['sent_at' => now()]);
        $this->assertFalse($this->campaign->hasTroublesSendingOutMails());
    }

    /** @test */
    public function it_can_inline_the_styles_of_the_html()
    {
        /** @var Campaign $campaign */
        $campaign = factory(Campaign::class)->create(['html' => '<<<HTML
                    <html>
                    <style>

                        body {
                            background-color: #e8eff6;
                            }
                    </style>
                    <body>My body</body>
                    </html>'

        ]);

        $this->assertMatchesHtmlSnapshotWithoutWhitespace($campaign->htmlWithInlinedCss());
    }

    private function assertModels(array $expectedModels, Collection $actualModels)
    {
        $this->assertEquals(count($expectedModels), $actualModels->count());
        $this->assertEquals(collect($expectedModels)->pluck('id')->toArray(), $actualModels->pluck('id')->toArray());
    }
}
