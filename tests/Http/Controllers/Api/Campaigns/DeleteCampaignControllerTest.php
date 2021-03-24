<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\Campaigns;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignsController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class DeleteCampaignControllerTest extends TestCase
{
    use RespondsToApiRequests;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();
    }

    /** @test */
    public function a_campaign_can_be_deleted_using_the_api()
    {
        $campaign = Campaign::factory()->create();

        $this
            ->deleteJson(action([CampaignsController::class, 'destroy'], $campaign))
            ->assertSuccessful();

        $this->assertCount(0, Campaign::all());
    }
}
