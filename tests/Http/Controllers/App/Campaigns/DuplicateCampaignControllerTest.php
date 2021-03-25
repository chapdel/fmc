<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\App\Campaigns;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignSettingsController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\DuplicateCampaignController;
use Spatie\Mailcoach\Tests\TestCase;

class DuplicateCampaignControllerTest extends TestCase
{
    /** @test */
    public function it_can_duplicate_a_campaign()
    {
        $this->authenticate();

        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $originalCampaign */
        $originalCampaign = Campaign::factory()->create();

        $response = $this
            ->post(action(DuplicateCampaignController::class, $originalCampaign->id));

        $duplicatedCampaign = Campaign::orderByDesc('id')->first();

        $response->assertRedirect(action([CampaignSettingsController::class, 'edit'], $duplicatedCampaign->id));

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