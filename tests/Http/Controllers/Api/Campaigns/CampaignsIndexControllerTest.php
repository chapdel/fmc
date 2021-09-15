<?php

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignsController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();
});

it('can list campaign', function () {
    $templates = Campaign::factory(3)->create();

    $this
        ->getJson(action([CampaignsController::class, 'index']))
        ->assertSuccessful()
        ->assertSeeText($templates->first()->name);
});

it('can search campaigns', function () {
    Campaign::factory()->create([
        'name' => 'one',
    ]);

    Campaign::factory()->create([
        'name' => 'two',
    ]);

    $this
        ->getJson(action([CampaignsController::class, 'index']) . '?filter[search]=two')
        ->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['name' => 'two']);
});
