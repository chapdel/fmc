<?php

namespace Spatie\Mailcoach\Tests\Feature\Controllers\App\Campaigns;

use Spatie\Mailcoach\Http\App\Controllers\Campaigns\DestroyCampaignController;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Tests\TestCase;

class DestroyCampaignControllerTest extends TestCase
{
    /** @test */
    public function it_can_delete_a_campaign()
    {
        $this->authenticate();

        $campaign = factory(Campaign::class)->create();

        $this
            ->delete(action(DestroyCampaignController::class, $campaign->id))
            ->assertRedirect();

        $this->assertCount(0, Campaign::get());
    }
}
