<?php

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignSettingsController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\DuplicateCampaignController;
use Spatie\Mailcoach\Tests\TestCase;


it('can duplicate a campaign', function () {
    test()->authenticate();

    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $originalCampaign */
    $originalCampaign = Campaign::factory()->create();

    $response = $this
        ->post(action(DuplicateCampaignController::class, $originalCampaign->id));

    $duplicatedCampaign = Campaign::orderByDesc('id')->first();

    $response->assertRedirect(action([CampaignSettingsController::class, 'edit'], $duplicatedCampaign->id));

    test()->assertEquals(
        "Duplicate of {$originalCampaign->name}",
        $duplicatedCampaign->name
    );

    foreach ([
                 'subject',
                 'email_list_id',
                 'html',
                 'webview_html',
                 'segment_class',
                 'segment_id',
             ] as $attribute) {
        test()->assertEquals($duplicatedCampaign->$attribute, $originalCampaign->$attribute);
    }
});
