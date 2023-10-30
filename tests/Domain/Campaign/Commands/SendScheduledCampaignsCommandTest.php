<?php

use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Campaign\Commands\SendScheduledCampaignsCommand;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\TestTime\TestTime;

beforeEach(function () {
    TestTime::freeze();

    Queue::fake();
});

it('will not send campaigns that are not scheduled', function () {
    $campaign = CampaignFactory::new()->create([
        'scheduled_at' => null,
        'status' => CampaignStatus::Draft,
    ]);

    test()->artisan(SendScheduledCampaignsCommand::class)->assertExitCode(0);
    $this->processQueuedJobs();

    expect($campaign->fresh()->status)->toEqual(CampaignStatus::Draft);
});

it('will not send a campaign that has a scheduled at in the future', function () {
    $campaign = CampaignFactory::new()->create([
        'scheduled_at' => now()->addSecond(),
        'status' => CampaignStatus::Draft,
    ]);

    test()->artisan(SendScheduledCampaignsCommand::class)->assertExitCode(0);
    $this->processQueuedJobs();

    expect($campaign->fresh()->status)->toEqual(CampaignStatus::Draft);
});

it('will send a campaign that has a scheduled at set to in the past', function () {
    $campaign = CampaignFactory::new()->create([
        'scheduled_at' => now()->subSecond(),
        'status' => CampaignStatus::Draft,
    ]);

    test()->artisan(SendScheduledCampaignsCommand::class)->assertExitCode(0);
    $this->processQueuedJobs();

    expect($campaign->fresh()->status)->toEqual(CampaignStatus::Sent);
});
