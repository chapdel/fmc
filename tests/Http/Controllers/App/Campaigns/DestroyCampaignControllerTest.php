<?php

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\DestroyCampaignController;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

it('can delete a campaign', function () {
    test()->authenticate();

    $campaign = Campaign::factory()->create();

    $this
        ->delete(action(DestroyCampaignController::class, $campaign->id))
        ->assertRedirect();

    expect(Campaign::get())->toHaveCount(0);
});
