<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\Campaigns;

use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignsController;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class CreateCampaignControllerTest extends TestCase
{
    use RespondsToApiRequests;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();
    }

    /** @test */
    public function a_campaign_can_be_created_using_the_api()
    {
        $attributes = [
            'name' => 'name',
            'email_list_id' => EmailList::factory()->create()->id,
            'html' => 'html',
            'track_opens' => true,
            'track_clicks' => false,
        ];

        $this
            ->postJson(action([CampaignsController::class, 'store']), $attributes)
            ->assertSuccessful();

        $campaign = Campaign::first();

        foreach ($attributes as $attributeName => $attributeValue) {
            $this->assertEquals($attributeValue, $campaign->$attributeName);
        }
    }
}