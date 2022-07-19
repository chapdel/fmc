<?php

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignsController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();
});

test('a campaign can be deleted using the api', function () {
    $campaign = Campaign::factory()->create();

    $this
        ->deleteJson(action([CampaignsController::class, 'destroy'], $campaign->uuid))
        ->assertSuccessful();

    expect(Campaign::all())->toHaveCount(0);
});
