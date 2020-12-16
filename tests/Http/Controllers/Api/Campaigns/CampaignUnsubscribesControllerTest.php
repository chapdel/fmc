<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\Campaigns;

use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignUnsubscribesController;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignUnsubscribe;
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
        $this
            ->getJson(action(CampaignUnsubscribesController::class, $this->campaignUnsubscribe->campaign))
            ->assertSuccessful()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'subscriber_id' => $this->campaignUnsubscribe->subscriber->id,
                'subscriber_email' => $this->campaignUnsubscribe->subscriber->email,
                'campaign_id' => $this->campaignUnsubscribe->campaign->id,
            ]);
    }
}
