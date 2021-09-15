<?php

use Spatie\Mailcoach\Domain\Campaign\Models\CampaignUnsubscribe;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignUnsubscribesController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);
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

    test()->assertEquals(test()->campaignUnsubscribe->subscriber->id, $response[0]['subscriber_id']);
    test()->assertEquals(test()->campaignUnsubscribe->subscriber->email, $response[0]['subscriber_email']);
    test()->assertEquals(test()->campaignUnsubscribe->campaign->id, $response[0]['campaign_id']);
});
