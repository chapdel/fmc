<?php

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
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\TestTime\TestTime;

test('a campaign with no subscribers will get all zeroes', function () {
    $campaign = Campaign::factory()->create();

    dispatch(new CalculateStatisticsJob($campaign));

    test()->assertDatabaseHas(static::getCampaignTableName(), [
        'id' => $campaign->id,
        'sent_to_number_of_subscribers' => 0,
        'open_count' => 0,
        'unique_open_count' => 0,
        'open_rate' => 0,
        'click_count' => 0,
        'unique_click_count' => 0,
        'click_rate' => 0,
    ]);
});

test('an automation mail with no subscribers will get all zeroes', function () {
    $automationMail = AutomationMail::factory()->create();

    dispatch(new CalculateStatisticsJob($automationMail));

    test()->assertDatabaseHas(static::getAutomationMailTableName(), [
        'id' => $automationMail->id,
        'sent_to_number_of_subscribers' => 0,
        'open_count' => 0,
        'unique_open_count' => 0,
        'open_rate' => 0,
        'click_count' => 0,
        'unique_click_count' => 0,
        'click_rate' => 0,
    ]);
});

it('will save the datetime when the statistics where calculated', function () {
    TestTime::freeze();

    $campaign = Campaign::factory()->create();
    expect($campaign->statistics_calculated_at)->toBeNull();

    $automationMail = AutomationMail::factory()->create();
    expect($automationMail->statistics_calculated_at)->toBeNull();

    dispatch(new CalculateStatisticsJob($campaign));
    dispatch(new CalculateStatisticsJob($automationMail));
    expect($campaign->fresh()->statistics_calculated_at)->toEqual(now()->format('Y-m-d H:i:s'));
    expect($automationMail->fresh()->statistics_calculated_at)->toEqual(now()->format('Y-m-d H:i:s'));
});

it('can calculate statistics regarding unsubscribes', function () {
    Subscriber::$fakeUuid = null;

    $campaign = (new CampaignFactory())->withSubscriberCount(5)->create();
    $automationMail = AutomationMail::factory()->create();

    $send = Send::factory()->create([
        'automation_mail_id' => $automationMail->id,
        'campaign_id' => null,
    ]);
    dispatch_now(new SendCampaignJob($campaign));
    dispatch_now(new SendAutomationMailJob($send));

    test()->assertDatabaseHas(static::getCampaignTableName(), [
        'id' => $campaign->id,
        'unsubscribe_count' => 0,
        'unsubscribe_rate' => 0,
    ]);

    test()->assertDatabaseHas(static::getAutomationMailTableName(), [
        'id' => $automationMail->id,
        'unsubscribe_count' => 0,
        'unsubscribe_rate' => 0,
    ]);

    $sends = $campaign->sends()->take(1)->get();
    test()->simulateUnsubscribes($sends);
    dispatch_now(new CalculateStatisticsJob($campaign));

    test()->simulateUnsubscribes(collect([$send]));
    dispatch_now(new CalculateStatisticsJob($automationMail));

    test()->assertDatabaseHas(static::getCampaignTableName(), [
        'id' => $campaign->id,
        'unsubscribe_count' => 1,
        'unsubscribe_rate' => 2000,
    ]);

    test()->assertDatabaseHas(static::getAutomationMailTableName(), [
        'id' => $automationMail->id,
        'unsubscribe_count' => 1,
        'unsubscribe_rate' => 10000,
    ]);
});

it('can calculate statistics regarding opens', function () {
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

    test()->assertDatabaseHas(static::getCampaignTableName(), [
        'id' => $campaign->id,
        'open_count' => 4,
        'unique_open_count' => 3,
        'open_rate' => 6000,
    ]);

    test()->assertDatabaseHas(static::getAutomationMailTableName(), [
        'id' => $automationMail->id,
        'open_count' => 2,
        'unique_open_count' => 1,
        'open_rate' => 10000,
    ]);
});

it('can calculate statistics regarding clicks', function () {
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
            simulateClick($campaign, $url, $subscribers);
        });
    simulateClick(
        $campaign,
        'https://spatie.be',
        $subscribers->take(1)
    );

    simulateClick($automationMail, 'https://spatie.be', $send->subscriber);
    simulateClick($automationMail, 'https://spatie.be', $send->subscriber);

    dispatch_now(new CalculateStatisticsJob($campaign));
    dispatch_now(new CalculateStatisticsJob($automationMail));

    test()->assertDatabaseHas(static::getCampaignTableName(), [
        'id' => $campaign->id,
        'sent_to_number_of_subscribers' => 5,
        'click_count' => 7,
        'unique_click_count' => 3,
        'click_rate' => 6000,
    ]);

    test()->assertDatabaseHas(static::getAutomationMailTableName(), [
        'id' => $automationMail->id,
        'sent_to_number_of_subscribers' => 1,
        'click_count' => 2,
        'unique_click_count' => 1,
        'click_rate' => 10000,
    ]);
});

it('can calculate statistics regarding clicks on individual links', function () {
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

    simulateClick($automationMail, $url, $send->subscriber);

    dispatch_now(new CalculateStatisticsJob($campaign));
    dispatch_now(new CalculateStatisticsJob($automationMail));

    $campaignLink = CampaignLink::where('url', $url)->first();
    $automationMailLink = AutomationMailLink::where('url', $url)->first();

    expect($campaignLink->click_count)->toEqual(3);
    expect($campaignLink->unique_click_count)->toEqual(2);

    expect($automationMailLink->click_count)->toEqual(1);
    expect($automationMailLink->unique_click_count)->toEqual(1);
});

it('can calculate statistics regarding bounces', function () {
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

    expect($campaign->bounce_count)->toEqual(1);
    expect($campaign->bounce_rate)->toEqual(3333);

    expect($automationMail->bounce_count)->toEqual(1);
    expect($automationMail->bounce_rate)->toEqual(10000);
});

test('the queue of the calculate statistics job can be configured', function () {
    Queue::fake();
    config()->set('mailcoach.shared.perform_on_queue.calculate_statistics_job', 'custom-queue');

    $campaign = Campaign::factory()->create();
    dispatch(new CalculateStatisticsJob($campaign));
    Queue::assertPushed(CalculateStatisticsJob::class);
});

// Helpers
function simulateOpen(Collection $sends)
{
    $sends->each(function (Send $send) {
        $send->registerOpen();
        TestTime::addSeconds(10);
    });

    return $this;
}

function simulateClick(Sendable $sendable, string $url, $subscribers)
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
