<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\Campaigns;

use Spatie\Mailcoach\Domain\Campaign\Models\CampaignUnsubscribe;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignUnsubscribesController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class CampaignUnsubscribesControllerTest extends TestCase
{
    use RespondsToApiRequests;

    protected CampaignUnsubscribe $campaignUnsubscribe;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();

        $this->campaignUnsubscribe = CampaignUnsubscribe::factory()->create();
    }

    /** @test */
    public function it_can_get_the_unsubscribes_of_a_campaign()
    {
        $response = $this
            ->getJson(action(CampaignUnsubscribesController::class, $this->campaignUnsubscribe->campaign))
            ->assertSuccessful()
            ->assertJsonCount(1, 'data')
            ->json('data');

        $this->assertEquals($this->campaignUnsubscribe->subscriber->id, $response[0]['subscriber_id']);
        $this->assertEquals($this->campaignUnsubscribe->subscriber->email, $response[0]['subscriber_email']);
        $this->assertEquals($this->campaignUnsubscribe->campaign->id, $response[0]['campaign_id']);
    }
}
