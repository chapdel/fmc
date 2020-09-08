<?php

namespace Spatie\Mailcoach\Tests\Features\Controllers\App\Campaigns;

use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignSettingsController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\DuplicateCampaignController;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Tests\TestCase;

class DuplicateCampaignControllerTest extends TestCase
{
    /** @test */
    public function it_can_duplicate_a_campaign()
    {
        $this->authenticate();

        /** @var \Spatie\Mailcoach\Models\Campaign $originalCampaign */
        $originalCampaign = Campaign::factory()->create();

        $this
            ->post(action(DuplicateCampaignController::class, $originalCampaign->id))
            ->assertRedirect(action([CampaignSettingsController::class, 'edit'], 2));

        $duplicatedCampaign = Campaign::find(2);

        $this->assertEquals(
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
            $this->assertEquals($duplicatedCampaign->$attribute, $originalCampaign->$attribute);
        }
    }
}
