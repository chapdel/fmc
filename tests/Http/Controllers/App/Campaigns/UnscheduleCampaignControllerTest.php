<?php

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignDeliveryController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\UnscheduleCampaignController;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

it('can unschedule a campaign', function () {
    test()->authenticate();

    $campaign = Campaign::factory()->create([
        'scheduled_at' => now()->format('Y-m-d H:i:s'),
    ]);

    $this
        ->post(action(UnscheduleCampaignController::class, $campaign->id))
        ->assertRedirect(action(CampaignDeliveryController::class, $campaign->id));

    test()->assertNull($campaign->refresh()->scheduled_at);
});
