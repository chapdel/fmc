<?php

use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\SendCampaignController;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignSummary;

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
        ->assertRedirect(action(CampaignSummary::class, test()->campaign->id));

    expect(test()->campaign->fresh()->status)->toBe(CampaignStatus::SENDING);
});
