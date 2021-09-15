<?php

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignContentController;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

it('can update the html of a campaign', function () {
    test()->authenticate();

    $campaign = Campaign::factory()->create();

    $attributes = [
        'html' => 'updated_html',
    ];

    $this
        ->put(
            action([CampaignContentController::class, 'update'], $campaign->id),
            $attributes
        )
        ->assertSessionHasNoErrors()
        ->assertRedirect(action([CampaignContentController::class, 'edit'], $campaign->id));

    test()->assertStringContainsString('updated_html', Campaign::first()->html);
});
