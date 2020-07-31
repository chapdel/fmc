<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\Campaigns;

use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignClicksController;
use Spatie\Mailcoach\Models\CampaignClick;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class CampaignClicksControllerTest extends TestCase
{
    use RespondsToApiRequests;

    protected CampaignClick $campaignClick;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();

        $this->campaignClick = factory(CampaignClick::class)->create();
    }

    /** @test */
    public function it_can_get_the_opens_of_a_campaign()
    {
        $this
            ->getJson(action(CampaignClicksController::class, $this->campaignClick->link->campaign))
            ->assertSuccessful()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure(['data' => [
                [
                    'url',
                    'unique_click_count',
                    'click_count',
                ],
            ]]);
    }
}
