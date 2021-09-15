<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignsController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();
});

test('a campaign can be updated using the api', function () {
    $campaign = Campaign::factory()->create();

    $attributes = [
        'name' => 'name',
        'email_list_id' => EmailList::factory()->create()->id,
        'html' => 'html',
        'track_opens' => true,
        'track_clicks' => false,
    ];

    $this
        ->putJson(action([CampaignsController::class, 'update'], $campaign), $attributes)
        ->assertSuccessful();

    $campaign = $campaign->fresh();

    foreach ($attributes as $attributeName => $attributeValue) {
        test()->assertEquals($attributeValue, $campaign->$attributeName);
    }
});
