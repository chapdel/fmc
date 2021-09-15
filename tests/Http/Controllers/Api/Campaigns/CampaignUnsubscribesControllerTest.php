<?php

use Spatie\Mailcoach\Domain\Campaign\Models\CampaignUnsubscribe;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignUnsubscribesController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();

    test()->campaignUnsubscribe = CampaignUnsubscribe::factory()->create();
});

it('can get the unsubscribes of a campaign', function () {
    $response = $this
        ->getJson(action(CampaignUnsubscribesController::class, test()->campaignUnsubscribe->campaign))
        ->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->json('data');

    expect($response[0]['subscriber_id'])->toEqual(test()->campaignUnsubscribe->subscriber->id);
    expect($response[0]['subscriber_email'])->toEqual(test()->campaignUnsubscribe->subscriber->email);
    expect($response[0]['campaign_id'])->toEqual(test()->campaignUnsubscribe->campaign->id);
});
