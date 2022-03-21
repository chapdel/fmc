<?php

use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\SendCampaignController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent\CampaignSummaryController;

beforeEach(function () {
    test()->authenticate();

    test()->campaign = Campaign::factory()->create([
        'status' => CampaignStatus::DRAFT,
    ]);

    Bus::fake();
});

it('can send a campaign', function () {
    $this
        ->post(action(SendCampaignController::class, test()->campaign->id))
        ->assertRedirect(action(CampaignSummaryController::class, test()->campaign->id));

    expect(test()->campaign->fresh()->status)->toBe(CampaignStatus::SENDING);
});
