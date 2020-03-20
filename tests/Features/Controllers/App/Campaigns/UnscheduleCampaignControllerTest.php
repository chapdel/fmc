<?php

namespace Spatie\Mailcoach\Tests\Feature\Controllers\App\Campaigns;

use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignDeliveryController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\UnscheduleCampaignController;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Tests\TestCase;

class UnscheduleCampaignControllerTest extends TestCase
{
    /** @test */
    public function it_can_unschedule_a_campaign()
    {
        $this->authenticate();

        $campaign = factory(Campaign::class)->create([
            'scheduled_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $this
            ->post(action(UnscheduleCampaignController::class, $campaign->id))
            ->assertRedirect(action(CampaignDeliveryController::class, $campaign->id));

        $this->assertNull($campaign->refresh()->scheduled_at);
    }
}
