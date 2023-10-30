<?php

use Spatie\Mailcoach\Domain\Content\Models\Unsubscribe;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignUnsubscribesController;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

it('can get the unsubscribes of a campaign', function () {
    $this->loginToApi();

    $campaign = CampaignFactory::new()->create();

    $unsubscribe = Unsubscribe::factory()->create([
        'content_item_id' => $campaign->contentItem->id,
    ]);

    $response = $this
        ->getJson(action(CampaignUnsubscribesController::class, $campaign))
        ->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->json('data');

    expect($response[0]['subscriber_uuid'])->toEqual($unsubscribe->subscriber->uuid);
    expect($response[0]['subscriber_email'])->toEqual($unsubscribe->subscriber->email);
    expect($response[0]['campaign_uuid'])->toEqual($campaign->uuid);
});
