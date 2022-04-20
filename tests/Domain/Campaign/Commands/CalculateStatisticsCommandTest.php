<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Domain\Campaign\Commands\CalculateStatisticsCommand;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\TestTime\TestTime;

beforeEach(function () {
    TestTime::freeze('Y-m-d H:i:s', '2019-01-01 00:00:00');
});

it('will recalculate statistics at the right time', function (
    Carbon $sentAt,
    ?Carbon $statisticsCalculatedAt,
    bool $jobShouldHaveBeenDispatched
) {
    Bus::fake();

    $campaign = Campaign::factory()->create([
        'status' => CampaignStatus::SENT,
        'sent_at' => $sentAt,
        'statistics_calculated_at' => $statisticsCalculatedAt,
    ]);

    Send::factory()->create(['campaign_id' => $campaign->id]);

    test()->artisan(CalculateStatisticsCommand::class)->assertExitCode(0);

    $jobShouldHaveBeenDispatched
        ? Bus::assertDispatched(CalculateStatisticsJob::class)
        : Bus::assertNotDispatched(CalculateStatisticsJob::class);
})->with('caseProvider');

it('can recalculate the statistics of a single campaign', function () {
    $campaign = Campaign::factory()->create([
        'status' => CampaignStatus::SENT,
        'sent_at' => now()->subYear(),
        'statistics_calculated_at' => null,
    ]);

    test()->artisan(CalculateStatisticsCommand::class, ['campaignId' => $campaign->id])->assertExitCode(0);

    test()->assertNotNull($campaign->refresh()->statistics_calculated_at);
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
