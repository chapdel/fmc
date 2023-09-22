<?php

use Spatie\Mailcoach\Domain\Content\Models\Open;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignOpensController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();

    test()->campaignOpen = Open::factory()->create();
});

it('can get the opens of a campaign', function () {
    $this
        ->getJson(action(CampaignOpensController::class, test()->campaignOpen->contentItem->model))
        ->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment([
            'subscriber_email' => test()->campaignOpen->subscriber->email,
            'open_count' => 1,
        ]);
});
