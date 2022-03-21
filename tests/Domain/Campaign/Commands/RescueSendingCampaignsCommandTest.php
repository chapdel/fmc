<?php

use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Domain\Campaign\Commands\RescueSendingCampaignsCommand;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\TestTime\TestTime;

beforeEach(function () {
    TestTime::freeze();

    Bus::fake();
});

it('will dispatch campaigns that have been sending for too long', function () {
    $campaign = Campaign::factory()->create([
        'status' => CampaignStatus::SENDING,
    ]);

    test()->artisan(RescueSendingCampaignsCommand::class)->assertExitCode(0);
    Bus::assertDispatchedTimes(SendCampaignJob::class, 0);

    $send = Send::factory()->create([
        'campaign_id' => $campaign->id,
        'sending_job_dispatched_at' => now()->subSeconds(10),
    ]);

    test()->artisan(RescueSendingCampaignsCommand::class)->assertExitCode(0);
    Bus::assertDispatchedTimes(SendCampaignJob::class, 0);

    $send->update(['sending_job_dispatched_at' => now()->subHour()]);

    test()->artisan(RescueSendingCampaignsCommand::class)->assertExitCode(0);
    Bus::assertDispatchedTimes(SendCampaignJob::class, 1);
});

it('will dispatch campaigns that have been creating sends for too long', function () {
    $campaign = Campaign::factory()->create([
        'status' => CampaignStatus::SENDING,
    ]);

    test()->artisan(RescueSendingCampaignsCommand::class)->assertExitCode(0);
    Bus::assertDispatchedTimes(SendCampaignJob::class, 0);

    $send = Send::factory()->create([
        'campaign_id' => $campaign->id,
        'sending_job_dispatched_at' => null,
        'created_at' => now()->subSeconds(10),
    ]);

    test()->artisan(RescueSendingCampaignsCommand::class)->assertExitCode(0);
    Bus::assertDispatchedTimes(SendCampaignJob::class, 0);

    $send->update(['created_at' => now()->subHour()]);

    test()->artisan(RescueSendingCampaignsCommand::class)->assertExitCode(0);
    Bus::assertDispatchedTimes(SendCampaignJob::class, 1);
});
