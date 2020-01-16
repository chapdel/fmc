<?php

namespace Spatie\Mailcoach\Tests\Feature\Controllers\App\Campaigns;

use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignSettingsController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\DuplicateCampaignController;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\Tag;
use Spatie\Mailcoach\Tests\TestCase;

class DuplicateCampaignControllerTest extends TestCase
{
    /** @test */
    public function it_can_duplicate_a_campaign()
    {
        $this->authenticate();

        /** @var \Spatie\Mailcoach\Models\Campaign $originalCampaign */
        $originalCampaign = factory(Campaign::class)->create();

        $tag = Tag::create(['name' => 'test', 'email_list_id' => $originalCampaign->email_list_id]);

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
