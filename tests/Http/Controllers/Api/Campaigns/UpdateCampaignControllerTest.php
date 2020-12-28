<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\Campaigns;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignsController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class UpdateCampaignControllerTest extends TestCase
{
    use RespondsToApiRequests;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();
    }

    /** @test */
    public function a_campaign_can_be_updated_using_the_api()
    {
        $campaign = Campaign::factory()->create();

        $attributes = [
            'name' => 'name',
            'email_list_id' => EmailList::factory()->create()->id,
            'html' => 'html',
            'track_opens' => true,
            'track_clicks' => false,
        ];

        $this
            ->putJson(action([CampaignsController::class, 'update'], $campaign), $attributes)
            ->assertSuccessful();

        $campaign = $campaign->fresh();

        foreach ($attributes as $attributeName => $attributeValue) {
            $this->assertEquals($attributeValue, $campaign->$attributeName);
        }
    }
}
