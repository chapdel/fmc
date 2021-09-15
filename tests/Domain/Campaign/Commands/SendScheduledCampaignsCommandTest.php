<?php

use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Domain\Campaign\Commands\SendScheduledCampaignsCommand;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

uses(TestCase::class);

beforeEach(function () {
    TestTime::freeze();

    Bus::fake();
});

it('will not send campaigns that are not scheduled', function () {
    Campaign::factory()->create([
        'scheduled_at' => null,
        'status' => CampaignStatus::DRAFT,
    ]);

    test()->artisan(SendScheduledCampaignsCommand::class)->assertExitCode(0);

    Bus::assertNotDispatched(SendCampaignJob::class);
});

it('will not send a campaign that has a scheduled at in the future', function () {
    Campaign::factory()->create([
        'scheduled_at' => now()->addSecond(),
        'status' => CampaignStatus::DRAFT,
    ]);

    test()->artisan(SendScheduledCampaignsCommand::class)->assertExitCode(0);

    Bus::assertNotDispatched(SendCampaignJob::class);
});

it('will send a campaign that has a scheduled at set to in the past', function () {
    Campaign::factory()->create([
        'scheduled_at' => now()->subSecond(),
        'status' => CampaignStatus::DRAFT,
    ]);

    test()->artisan(SendScheduledCampaignsCommand::class)->assertExitCode(0);

    Bus::assertDispatched(SendCampaignJob::class);
});

it('will not send a campaign twice', function () {
    Campaign::factory()->create([
        'scheduled_at' => now()->subSecond(),
        'status' => CampaignStatus::DRAFT,
    ]);

    test()->artisan(SendScheduledCampaignsCommand::class)->assertExitCode(0);
    Bus::assertDispatchedTimes(SendCampaignJob::class, 1);

    test()->artisan(SendScheduledCampaignsCommand::class)->assertExitCode(0);
    Bus::assertDispatchedTimes(SendCampaignJob::class, 1);
});
