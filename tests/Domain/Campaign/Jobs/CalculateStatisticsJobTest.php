<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Jobs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailJob;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailLink;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignLink;
use Spatie\Mailcoach\Domain\Shared\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class CalculateStatisticsJobTest extends TestCase
{
    /** @test */
    public function a_campaign_with_no_subscribers_will_get_all_zeroes()
    {
        $campaign = Campaign::factory()->create();

        dispatch(new CalculateStatisticsJob($campaign));

        $this->assertDatabaseHas(static::getCampaignTableName(), [
            'id' => $campaign->id,
            'sent_to_number_of_subscribers' => 0,
            'open_count' => 0,
            'unique_open_count' => 0,
            'open_rate' => 0,
            'click_count' => 0,
            'unique_click_count' => 0,
            'click_rate' => 0,
        ]);
    }

    /** @test */
    public function an_automation_mail_with_no_subscribers_will_get_all_zeroes()
    {
        $automationMail = AutomationMail::factory()->create();

        dispatch(new CalculateStatisticsJob($automationMail));

        $this->assertDatabaseHas(static::getAutomationMailTableName(), [
            'id' => $automationMail->id,
            'sent_to_number_of_subscribers' => 0,
            'open_count' => 0,
            'unique_open_count' => 0,
            'open_rate' => 0,
            'click_count' => 0,
            'unique_click_count' => 0,
            'click_rate' => 0,
        ]);
    }

    /** @test */
    public function it_will_save_the_datetime_when_the_statistics_where_calculated()
    {
        TestTime::freeze();

        $campaign = Campaign::factory()->create();
        $this->assertNull($campaign->statistics_calculated_at);

        $automationMail = AutomationMail::factory()->create();
        $this->assertNull($automationMail->statistics_calculated_at);

        dispatch(new CalculateStatisticsJob($campaign));
        dispatch(new CalculateStatisticsJob($automationMail));
        $this->assertEquals(now()->format('Y-m-d H:i:s'), $campaign->fresh()->statistics_calculated_at);
        $this->assertEquals(now()->format('Y-m-d H:i:s'), $automationMail->fresh()->statistics_calculated_at);
    }

    /** @test */
    public function it_can_calculate_statistics_regarding_unsubscribes()
    {
        Subscriber::$fakeUuid = null;

        $campaign = (new CampaignFactory())->withSubscriberCount(5)->create();
        $automationMail = AutomationMail::factory()->create();

        $send = Send::factory()->create([
            'automation_mail_id' => $automationMail->id,
            'campaign_id' => null,
        ]);
        dispatch_now(new SendCampaignJob($campaign));
        dispatch_now(new SendAutomationMailJob($send));

        $this->assertDatabaseHas(static::getCampaignTableName(), [
            'id' => $campaign->id,
            'unsubscribe_count' => 0,
            'unsubscribe_rate' => 0,
        ]);

        $this->assertDatabaseHas(static::getAutomationMailTableName(), [
            'id' => $automationMail->id,
            'unsubscribe_count' => 0,
            'unsubscribe_rate' => 0,
        ]);

        $sends = $campaign->sends()->take(1)->get();
        $this->simulateUnsubscribes($sends);
        dispatch_now(new CalculateStatisticsJob($campaign));

        $this->simulateUnsubscribes(collect([$send]));
        dispatch_now(new CalculateStatisticsJob($automationMail));

        $this->assertDatabaseHas(static::getCampaignTableName(), [
            'id' => $campaign->id,
            'unsubscribe_count' => 1,
            'unsubscribe_rate' => 2000,
        ]);

        $this->assertDatabaseHas(static::getAutomationMailTableName(), [
            'id' => $automationMail->id,
            'unsubscribe_count' => 1,
            'unsubscribe_rate' => 10000,
        ]);
    }

    /** @test */
    public function it_can_calculate_statistics_regarding_opens()
    {
        $campaign = (new CampaignFactory())->withSubscriberCount(5)->create(['track_opens' => true]);
        dispatch(new SendCampaignJob($campaign));

        $automationMail = AutomationMail::factory()->create([
            'track_opens' => true,
        ]);
        dispatch(new SendAutomationMailJob(Send::factory()->create([
            'automation_mail_id' => $automationMail->id,
            'campaign_id' => null,
        ])));

        $sends = $campaign->sends()->take(3)->get();
        $this
            ->simulateOpen($sends)
            ->simulateOpen($sends->take(1));

        $sends = $automationMail->sends()->take(1)->get();
        $this
            ->simulateOpen($sends)
            ->simulateOpen($sends->take(1));

        dispatch(new CalculateStatisticsJob($campaign));
        dispatch(new CalculateStatisticsJob($automationMail));

        $this->assertDatabaseHas(static::getCampaignTableName(), [
            'id' => $campaign->id,
            'open_count' => 4,
            'unique_open_count' => 3,
            'open_rate' => 6000,
        ]);

        $this->assertDatabaseHas(static::getAutomationMailTableName(), [
            'id' => $automationMail->id,
            'open_count' => 2,
            'unique_open_count' => 1,
            'open_rate' => 10000,
        ]);
    }

    /** @test */
    public function it_can_calculate_statistics_regarding_clicks()
    {
        $campaign = (new CampaignFactory())->withSubscriberCount(5)->create([
            'html' => '<a href="https://spatie.be">Spatie</a><a href="https://flareapp.io">Flare</a><a href="https://docs.spatie.be">Docs</a>',
            'track_clicks' => true,
        ]);
        dispatch(new SendCampaignJob($campaign));

        $automationMail = AutomationMail::factory()->create([
            'html' => '<a href="https://spatie.be">Spatie</a><a href="https://flareapp.io">Flare</a><a href="https://docs.spatie.be">Docs</a>',
            'track_clicks' => true,
        ]);
        $send = Send::factory()->create([
            'automation_mail_id' => $automationMail->id,
            'campaign_id' => null,
        ]);
        dispatch(new SendAutomationMailJob($send));

        $subscribers = $campaign->emailList->subscribers->take(3);
        collect(['https://spatie.be', 'https://example.com'])
            ->each(function (string $url) use ($campaign, $subscribers) {
                $this->simulateClick($campaign, $url, $subscribers);
            });
        $this->simulateClick(
            $campaign,
            'https://spatie.be',
            $subscribers->take(1)
        );

        $this->simulateClick($automationMail, 'https://spatie.be', $send->subscriber);
        $this->simulateClick($automationMail, 'https://spatie.be', $send->subscriber);

        dispatch_now(new CalculateStatisticsJob($campaign));
        dispatch_now(new CalculateStatisticsJob($automationMail));

        $this->assertDatabaseHas(static::getCampaignTableName(), [
            'id' => $campaign->id,
            'sent_to_number_of_subscribers' => 5,
            'click_count' => 7,
            'unique_click_count' => 3,
            'click_rate' => 6000,
        ]);

        $this->assertDatabaseHas(static::getAutomationMailTableName(), [
            'id' => $automationMail->id,
            'sent_to_number_of_subscribers' => 1,
            'click_count' => 2,
            'unique_click_count' => 1,
            'click_rate' => 10000,
        ]);
    }

    /** @test */
    public function it_can_calculate_statistics_regarding_clicks_on_individual_links()
    {
        $campaign = (new CampaignFactory())->withSubscriberCount(3)->create([
            'html' => '<a href="https://spatie.be">Spatie</a>',
            'track_clicks' => true,
        ]);
        dispatch(new SendCampaignJob($campaign));

        $automationMail = AutomationMail::factory()->create([
            'html' => '<a href="https://spatie.be">Spatie</a><a href="https://flareapp.io">Flare</a><a href="https://docs.spatie.be">Docs</a>',
            'track_clicks' => true,
        ]);
        $send = Send::factory()->create([
            'automation_mail_id' => $automationMail->id,
            'campaign_id' => null,
        ]);
        dispatch(new SendAutomationMailJob($send));

        $subscriber1 = $campaign->emailList->subscribers[0];
        $subscriber2 = $campaign->emailList->subscribers[1];
        $subscriber3 = $campaign->emailList->subscribers[2];

        $url = 'https://spatie.be';

        $this
            ->simulateClick($campaign, $url, $subscriber1)
            ->simulateClick($campaign, $url, $subscriber2)
            ->simulateClick($campaign, $url, $subscriber2);

        $this->simulateClick($automationMail, $url, $send->subscriber);

        dispatch_now(new CalculateStatisticsJob($campaign));
        dispatch_now(new CalculateStatisticsJob($automationMail));

        $campaignLink = CampaignLink::where('url', $url)->first();
        $automationMailLink = AutomationMailLink::where('url', $url)->first();

        $this->assertEquals(3, $campaignLink->click_count);
        $this->assertEquals(2, $campaignLink->unique_click_count);

        $this->assertEquals(1, $automationMailLink->click_count);
        $this->assertEquals(1, $automationMailLink->unique_click_count);
    }

    /** @test */
    public function it_can_calculate_statistics_regarding_bounces()
    {
        $campaign = (new CampaignFactory())->withSubscriberCount(3)->create([
            'html' => '<a href="https://spatie.be">Spatie</a>',
            'track_clicks' => true,
        ]);

        $automationMail = AutomationMail::factory()->create([
            'html' => '<a href="https://spatie.be">Spatie</a>',
            'track_clicks' => true,
        ]);
        $send = Send::factory()->create([
            'automation_mail_id' => $automationMail->id,
            'campaign_id' => null,
        ]);

        dispatch(new SendCampaignJob($campaign));
        dispatch(new SendAutomationMailJob($send));

        $campaign->sends()->first()->registerBounce();
        $automationMail->sends()->first()->registerBounce();

        dispatch_now(new CalculateStatisticsJob($campaign));
        dispatch_now(new CalculateStatisticsJob($automationMail));

        $this->assertEquals(1, $campaign->bounce_count);
        $this->assertEquals(3333, $campaign->bounce_rate);

        $this->assertEquals(1, $automationMail->bounce_count);
        $this->assertEquals(10000, $automationMail->bounce_rate);
    }

    /** @test */
    public function the_queue_of_the_calculate_statistics_job_can_be_configured()
    {
        Queue::fake();
        config()->set('mailcoach.shared.perform_on_queue.calculate_statistics_job', 'custom-queue');

        $campaign = Campaign::factory()->create();
        dispatch(new CalculateStatisticsJob($campaign));
        Queue::assertPushed(CalculateStatisticsJob::class);
    }

    protected function simulateOpen(Collection $sends)
    {
        $sends->each(function (Send $send) {
            $send->registerOpen();
            TestTime::addSeconds(10);
        });

        return $this;
    }

    public function simulateClick(Sendable $sendable, string $url, $subscribers)
    {
        if ($subscribers instanceof Model) {
            $subscribers = collect([$subscribers]);
        }

        collect($subscribers)->each(function (Subscriber $subscriber) use ($sendable, $url) {
            Send::query()
                ->when($sendable::class === Campaign::class, function ($query) use ($sendable) {
                    $query->where('campaign_id', $sendable->id);
                })
                ->when($sendable::class === AutomationMail::class, function ($query) use ($sendable) {
                    $query->where('automation_mail_id', $sendable->id);
                })
                ->where('subscriber_id', $subscriber->id)
                ->first()
                ->registerClick($url);
        });

        return $this;
    }
}
