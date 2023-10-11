<?php

use Spatie\Mailcoach\Domain\Content\Models\Click;
use Spatie\Mailcoach\Domain\Content\Models\Link;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignClicksController;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();

    test()->campaign = CampaignFactory::new()->create();

    test()->campaignClick = Click::factory()->create([
        'link_id' => Link::factory()->create([
            'content_item_id' => test()->campaign->contentItem->id,
        ]),
    ]);
});

it('can get the clicks of a campaign', function () {
    $this
        ->getJson(action(CampaignClicksController::class, test()->campaign))
        ->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonStructure(['data' => [
            [
                'uuid',
                'url',
                'unique_click_count',
                'click_count',
                'clicks',
            ],
        ]]);
});
