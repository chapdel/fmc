<?php

use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\SendCampaignController;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    Bus::fake();

    test()->loginToApi();

    test()->campaign = CampaignFactory::new()->create([
        'status' => CampaignStatus::Draft,
    ]);
});

test('a campaign can be sent using the api', function () {
    $this
        ->postJson(action(SendCampaignController::class, test()->campaign))
        ->assertSuccessful();

    expect(test()->campaign->fresh()->status)->toBe(CampaignStatus::Sending);
});

it('will not send a campaign that has already been sent', function () {
    test()->withExceptionHandling();

    test()->campaign->update(['status' => CampaignStatus::Sent]);

    $this
        ->postJson(action(SendCampaignController::class, test()->campaign))
        ->assertJsonValidationErrors('campaign');
});
