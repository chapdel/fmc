<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\Campaigns;

use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignsController;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\UsesApi;
use Spatie\Mailcoach\Tests\TestCase;

class UpdateCampaignControllerTest extends TestCase
{
    use UsesApi;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();
    }

    /** @test */
    public function a_campaign_can_be_updated_using_the_api()
    {
        $campaign = factory(Campaign::class)->create();

        $attributes = [
            'name' => 'name',
            'email_list_id' => factory(EmailList::class)->create()->id,
            'html' => 'html',
            'track_opens' => true,
            'track_clicks' => false,
        ];

        $this
            ->postJson(action([CampaignsController::class, 'update'], $campaign), $attributes)
            ->assertSuccessful();

        $campaign = $campaign->fresh();

        foreach ($attributes as $attributeName => $attributeValue) {
            $this->assertEquals($attributeValue, $campaign->$attributeName);
        }
    }
}
