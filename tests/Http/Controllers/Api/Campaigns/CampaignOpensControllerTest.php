<?php

use Spatie\Mailcoach\Domain\Campaign\Models\CampaignOpen;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignOpensController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);
uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();

    test()->campaignOpen = CampaignOpen::factory()->create();
});

it('can get the opens of a campaign', function () {
    $this
        ->getJson(action(CampaignOpensController::class, test()->campaignOpen->campaign))
        ->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment([
            'subscriber_email' => test()->campaignOpen->subscriber->email,
            'open_count' => 1,
        ]);
});
