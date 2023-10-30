<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailJob;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Domain\Content\Models\Link;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\TestTime\TestTime;

test('a campaign with no subscribers will get all zeroes', function () {
    $campaign = Campaign::factory()->create();

    dispatch(new CalculateStatisticsJob($campaign->contentItem));

    test()->assertDatabaseHas(static::getContentItemTableName(), [
        'id' => $campaign->contentItem->id,
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

    dispatch(new CalculateStatisticsJob($automationMail->contentItem));

    test()->assertDatabaseHas(static::getContentItemTableName(), [
        'id' => $automationMail->contentItem->id,
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

    dispatch(new CalculateStatisticsJob($campaign->contentItem));
    dispatch(new CalculateStatisticsJob($automationMail->contentItem));
    expect($campaign->fresh()->contentItem->statistics_calculated_at)->toEqual(now()->format('Y-m-d H:i:s'));
    expect($automationMail->fresh()->contentItem->statistics_calculated_at)->toEqual(now()->format('Y-m-d H:i:s'));
});

it('can calculate statistics regarding unsubscribes', function () {
    $campaign = (new CampaignFactory())->withSubscriberCount(5)->create();
    $automationMail = AutomationMail::factory()->create();

    $send = Send::factory()->create([
        'content_item_id' => $automationMail->contentItem->id,
    ]);
    $campaign->send();
    Artisan::call('mailcoach:send-scheduled-campaigns');
    dispatch_sync(new SendAutomationMailJob($send));

    test()->assertDatabaseHas(static::getContentItemTableName(), [
        'id' => $campaign->contentItem->id,
        'unsubscribe_count' => 0,
        'unsubscribe_rate' => 0,
    ]);

    test()->assertDatabaseHas(static::getContentItemTableName(), [
        'id' => $automationMail->contentItem->id,
        'unsubscribe_count' => 0,
        'unsubscribe_rate' => 0,
    ]);

    $sends = $campaign->contentItem->sends()->take(1)->get();
    test()->simulateUnsubscribes($sends);
    dispatch_sync(new CalculateStatisticsJob($campaign->contentItem));

    test()->simulateUnsubscribes(collect([$send]));
    dispatch_sync(new CalculateStatisticsJob($automationMail->contentItem));

    test()->assertDatabaseHas(static::getContentItemTableName(), [
        'id' => $campaign->contentItem->id,
        'unsubscribe_count' => 1,
        'unsubscribe_rate' => 2000,
    ]);

    test()->assertDatabaseHas(static::getContentItemTableName(), [
        'id' => $automationMail->contentItem->id,
        'unsubscribe_count' => 1,
        'unsubscribe_rate' => 10000,
    ]);
});

it('can calculate statistics regarding opens', function () {
    $campaign = (new CampaignFactory())->withSubscriberCount(5)->create();
    $campaign->send();
    Artisan::call('mailcoach:send-scheduled-campaigns');

    $automationMail = AutomationMail::factory()->create();
    dispatch(new SendAutomationMailJob(Send::factory()->create([
        'content_item_id' => $automationMail->contentItem->id,
    ])));

    $sends = $campaign->contentItem->sends()->take(3)->get();

    simulateOpen($sends);
    simulateOpen($sends->take(1));

    $sends = $automationMail->contentItem->sends()->take(1)->get();

    simulateOpen($sends);
    simulateOpen($sends->take(1));

    dispatch(new CalculateStatisticsJob($campaign->contentItem));
    dispatch(new CalculateStatisticsJob($automationMail->contentItem));

    test()->assertDatabaseHas(static::getContentItemTableName(), [
        'id' => $campaign->contentItem->id,
        'open_count' => 4,
        'unique_open_count' => 3,
        'open_rate' => 6000,
    ]);

    test()->assertDatabaseHas(static::getContentItemTableName(), [
        'id' => $automationMail->contentItem->id,
        'open_count' => 2,
        'unique_open_count' => 1,
        'open_rate' => 10000,
    ]);
});

it('can calculate statistics regarding clicks', function () {
    $campaign = (new CampaignFactory())->withSubscriberCount(5)->create([
        'html' => '<a href="https://spatie.be">Spatie</a><a href="https://flareapp.io">Flare</a><a href="https://docs.spatie.be">Docs</a>',
    ]);
    $campaign->send();
    Artisan::call('mailcoach:send-scheduled-campaigns');

    $automationMail = AutomationMail::factory()->create();
    $automationMail->contentItem->update([
        'html' => '<a href="https://spatie.be">Spatie</a><a href="https://flareapp.io">Flare</a><a href="https://docs.spatie.be">Docs</a>',
    ]);
    $send = Send::factory()->create([
        'content_item_id' => $automationMail->contentItem->id,
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

    dispatch_sync(new CalculateStatisticsJob($campaign->contentItem));
    dispatch_sync(new CalculateStatisticsJob($automationMail->contentItem));

    test()->assertDatabaseHas(static::getContentItemTableName(), [
        'id' => $campaign->contentItem->id,
        'sent_to_number_of_subscribers' => 5,
        'click_count' => 7,
        'unique_click_count' => 3,
        'click_rate' => 6000,
    ]);

    test()->assertDatabaseHas(static::getContentItemTableName(), [
        'id' => $automationMail->contentItem->id,
        'sent_to_number_of_subscribers' => 1,
        'click_count' => 2,
        'unique_click_count' => 1,
        'click_rate' => 10000,
    ]);
});

it('can calculate statistics regarding clicks on individual links', function () {
    $campaign = (new CampaignFactory())->withSubscriberCount(3)->create([
        'html' => '<a href="https://spatie.be">Spatie</a>',
    ]);
    $campaign->send();
    Artisan::call('mailcoach:send-scheduled-campaigns');

    $automationMail = AutomationMail::factory()->create();
    $automationMail->contentItem->update([
        'html' => '<a href="https://spatie.be">Spatie</a><a href="https://flareapp.io">Flare</a><a href="https://docs.spatie.be">Docs</a>',
    ]);
    $send = Send::factory()->create([
        'content_item_id' => $automationMail->contentItem->id,
    ]);
    dispatch(new SendAutomationMailJob($send));

    $subscriber1 = $campaign->emailList->subscribers[0];
    $subscriber2 = $campaign->emailList->subscribers[1];
    $subscriber3 = $campaign->emailList->subscribers[2];

    $url = 'https://spatie.be';

    simulateClick($campaign, $url, $subscriber1);
    simulateClick($campaign, $url, $subscriber2);
    simulateClick($campaign, $url, $subscriber2);

    simulateClick($automationMail, $url, $send->subscriber);

    dispatch_sync(new CalculateStatisticsJob($campaign->contentItem));
    dispatch_sync(new CalculateStatisticsJob($automationMail->contentItem));

    $campaignLink = Link::where('url', $url)->where('content_item_id', $campaign->contentItem->id)->first();
    $automationMailLink = Link::where('url', $url)->where('content_item_id', $automationMail->contentItem->id)->first();

    expect($campaignLink->click_count)->toEqual(3);
    expect($campaignLink->unique_click_count)->toEqual(2);

    expect($automationMailLink->click_count)->toEqual(1);
    expect($automationMailLink->unique_click_count)->toEqual(1);
});

it('can calculate statistics regarding bounces', function () {
    $campaign = (new CampaignFactory())->withSubscriberCount(3)->create([
        'html' => '<a href="https://spatie.be">Spatie</a>',
    ]);

    $automationMail = AutomationMail::factory()->create();
    $automationMail->contentItem->update([
        'html' => '<a href="https://spatie.be">Spatie</a>',
    ]);
    $send = Send::factory()->create([
        'content_item_id' => $automationMail->contentItem->id,
    ]);

    $campaign->send();
    Artisan::call('mailcoach:send-scheduled-campaigns');
    dispatch(new SendAutomationMailJob($send));

    $campaign->contentItem->sends()->first()->registerBounce();
    // A duplicate will only count once
    $campaign->contentItem->sends()->first()->registerBounce();
    $automationMail->contentItem->sends()->first()->registerBounce();

    (new CalculateStatisticsJob($campaign->contentItem))->handle();
    (new CalculateStatisticsJob($automationMail->contentItem))->handle();

    expect($campaign->contentItem->bounce_count)->toEqual(1);
    expect($campaign->contentItem->bounce_rate)->toEqual(3333);

    expect($automationMail->contentItem->bounce_count)->toEqual(1);
    expect($automationMail->contentItem->bounce_rate)->toEqual(10000);
});

test('the queue of the calculate statistics job can be configured', function () {
    Queue::fake();
    config()->set('mailcoach.perform_on_queue.calculate_statistics_job', 'custom-queue');

    $campaign = Campaign::factory()->create();
    dispatch(new CalculateStatisticsJob($campaign->contentItem));
    Queue::assertPushed(CalculateStatisticsJob::class);
});

// Helpers
function simulateOpen(Collection $sends)
{
    $sends->each(function (Send $send) {
        $send->registerOpen();
        TestTime::addSeconds(10);
    });
}

function simulateClick(Sendable $sendable, string $url, $subscribers)
{
    if ($subscribers instanceof Model) {
        $subscribers = collect([$subscribers]);
    }

    collect($subscribers)->each(function (Subscriber $subscriber) use ($sendable, $url) {
        Send::query()
            ->where('content_item_id', $sendable->contentItem->id)
            ->where('subscriber_id', $subscriber->id)
            ->first()
            ->registerClick($url);
    });
}
