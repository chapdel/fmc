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
        'email_list_uuid' => EmailList::factory()->create()->uuid,
        'html' => 'html',
        'schedule_at' => '2022-01-01 10:00:00',
    ];

    $this
        ->putJson(action([CampaignsController::class, 'update'], $campaign->uuid), $attributes)
        ->assertSuccessful();

    $campaign = $campaign->fresh();

    foreach ($attributes as $attributeName => $attributeValue) {
        if ($attributeName === 'schedule_at') {
            $attributeName = 'scheduled_at';
        }

        if ($attributeName === 'email_list_uuid') {
            test()->assertEquals($attributeValue, $campaign->emailList->uuid);
            continue;
        }

        test()->assertEquals($attributeValue, $campaign->$attributeName);
    }
});
