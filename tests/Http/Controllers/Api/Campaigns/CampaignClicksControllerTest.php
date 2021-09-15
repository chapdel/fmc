<?php

use Spatie\Mailcoach\Domain\Campaign\Models\CampaignClick;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignClicksController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();

    test()->campaignClick = CampaignClick::factory()->create();
});

it('can get the opens of a campaign', function () {
    $this
        ->getJson(action(CampaignClicksController::class, test()->campaignClick->link->campaign))
        ->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonStructure(['data' => [
            [
                'url',
                'unique_click_count',
                'click_count',
            ],
        ]]);
});
