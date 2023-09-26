<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Campaign\Commands\CalculateStatisticsCommand;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Content\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\TestTime\TestTime;

beforeEach(function () {
    TestTime::freeze('Y-m-d H:i:s', '2019-01-01 00:00:00');
});

it('will recalculate statistics at the right time', function (
    Carbon $sentAt,
    ?Carbon $statisticsCalculatedAt,
    bool $jobShouldHaveBeenDispatched
) {
    Queue::fake();

    $campaign = CampaignFactory::new()->create([
        'status' => CampaignStatus::Sent,
        'sent_at' => $sentAt,
        'all_sends_dispatched_at' => $sentAt,
        'all_sends_created_at' => $sentAt,
        'statistics_calculated_at' => $statisticsCalculatedAt,
    ]);

    Send::factory()->create(['content_item_id' => $campaign->contentItem->id]);

    test()->artisan(CalculateStatisticsCommand::class)->assertExitCode(0);
    $this->processQueuedJobs();

    $jobShouldHaveBeenDispatched
        ? Queue::assertPushed(CalculateStatisticsJob::class)
        : Queue::assertNotPushed(CalculateStatisticsJob::class);
})->with('caseProvider');

it('will not calculate statistics of campaigns that are not sent or cancelled', function () {
    Queue::fake();

    $campaign = CampaignFactory::new()->create([
        'status' => CampaignStatus::Sent,
        'sent_at' => now()->subMinutes(2),
        'all_sends_dispatched_at' => now()->subMinutes(2),
        'all_sends_created_at' => now()->subMinutes(2),
        'statistics_calculated_at' => now()->subMinute(),
    ]);

    CampaignFactory::new()->create([
        'status' => CampaignStatus::Cancelled,
        'sent_at' => now()->subMinutes(2),
        'all_sends_dispatched_at' => now()->subMinutes(2),
        'all_sends_created_at' => now()->subMinutes(2),
        'statistics_calculated_at' => now()->subMinute(),
    ]);

    CampaignFactory::new()->create([
        'status' => CampaignStatus::Draft,
        'sent_at' => now()->subMinutes(2),
        'all_sends_dispatched_at' => now()->subMinutes(2),
        'all_sends_created_at' => now()->subMinutes(2),
        'statistics_calculated_at' => now()->subMinute(),
    ]);

    Send::factory()->create(['content_item_id' => $campaign->contentItem->id]);

    test()->artisan(CalculateStatisticsCommand::class)->assertExitCode(0);
    $this->processQueuedJobs();

    Queue::assertPushed(CalculateStatisticsJob::class, 2);
});

it('can recalculate the statistics of a single campaign', function () {
    $campaign = CampaignFactory::new()->create([
        'sent_at' => now()->subYear(),
        'all_sends_dispatched_at' => now()->subYear(),
        'all_sends_created_at' => now()->subYear(),
        'statistics_calculated_at' => null,
    ]);

    test()->artisan(CalculateStatisticsCommand::class, ['campaignId' => $campaign->id])->assertExitCode(0);

    expect($campaign->refresh()->contentItem->statistics_calculated_at)->not->toBeNull();
});

// Datasets
dataset('caseProvider', function () {
    TestTime::freeze('Y-m-d H:i:s', '2019-01-01 00:00:00');

    return [
        [now()->subSecond(), null, true],

        [now()->subMinutes(2), now()->subMinute(), true],

        [now()->subMinutes(5), now()->subMinutes(1), true],
        [now()->subMinutes(6), now()->subMinutes(1), false],

        [now()->subMinutes(20), now()->subMinutes(9), false],
        [now()->subMinutes(20), now()->subMinutes(10)->subSecond(), true],

        [now()->subHours(3), now()->subHour(), false],
        [now()->subHours(3), now()->subHour()->subSecond(), true],

        [now()->subDay()->subMinute(), now()->subHours(4), false],
        [now()->subDay()->subMinute(), now()->subHours(4)->subSecond(), true],

        [now()->subWeeks(2), now()->subDay(), true],
        [now()->subWeeks(2)->subSecond(), now()->subDay(), false],

    ];
});
