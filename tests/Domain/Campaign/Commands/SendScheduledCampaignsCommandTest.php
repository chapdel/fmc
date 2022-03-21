<?php

use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Domain\Campaign\Commands\SendScheduledCampaignsCommand;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\TestTime\TestTime;

beforeEach(function () {
    TestTime::freeze();

    Bus::fake();
});

it('will not send campaigns that are not scheduled', function () {
    $campaign = Campaign::factory()->create([
        'scheduled_at' => null,
        'status' => CampaignStatus::DRAFT,
    ]);

    test()->artisan(SendScheduledCampaignsCommand::class)->assertExitCode(0);

    expect($campaign->fresh()->status)->toEqual(CampaignStatus::DRAFT);
});

it('will not send a campaign that has a scheduled at in the future', function () {
    $campaign = Campaign::factory()->create([
        'scheduled_at' => now()->addSecond(),
        'status' => CampaignStatus::DRAFT,
    ]);

    test()->artisan(SendScheduledCampaignsCommand::class)->assertExitCode(0);

    expect($campaign->fresh()->status)->toEqual(CampaignStatus::DRAFT);
});

it('will send a campaign that has a scheduled at set to in the past', function () {
    $campaign = Campaign::factory()->create([
        'scheduled_at' => now()->subSecond(),
        'status' => CampaignStatus::DRAFT,
    ]);

    test()->artisan(SendScheduledCampaignsCommand::class)->assertExitCode(0);

    expect($campaign->fresh()->status)->toEqual(CampaignStatus::SENT);
});

