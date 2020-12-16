<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\Campaigns;

use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignOpensController;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignOpen;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class CampaignOpensControllerTest extends TestCase
{
    use RespondsToApiRequests;

    protected CampaignOpen $campaignOpen;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();

        $this->campaignOpen = CampaignOpen::factory()->create();
    }

    /** @test */
    public function it_can_get_the_opens_of_a_campaign()
    {
        $this
            ->getJson(action(CampaignOpensController::class, $this->campaignOpen->campaign))
            ->assertSuccessful()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'subscriber_email' => $this->campaignOpen->subscriber->email,
                'open_count' => 1,
            ]);
    }
}
