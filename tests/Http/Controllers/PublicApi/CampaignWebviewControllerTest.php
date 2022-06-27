<?php

use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Front\Controllers\CampaignWebviewController;

beforeEach(function () {
    test()->campaign = Campaign::factory()->create([
        'webview_html' => 'my webview html',
    ]);

    test()->campaign->markAsSent(1);

    test()->webviewUrl = action(CampaignWebviewController::class, test()->campaign->uuid);
});

it('can display the webview for a campaign', function () {
    $this
        ->get(test()->webviewUrl)
        ->assertSuccessful()
        ->assertSee('my webview html');
});

it('will not display a webview for a campaign that has not been sent', function () {
    test()->withExceptionHandling();

    test()->campaign->update(['status' => CampaignStatus::Draft]);

    $this
        ->get(test()->webviewUrl)
        ->assertStatus(404);
});
