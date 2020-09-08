<?php

namespace Spatie\Mailcoach\Tests\Features\Controllers\App\Campaigns;

use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Enums\CampaignStatus;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\SendCampaignController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent\CampaignSummaryController;
use Spatie\Mailcoach\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Tests\TestCase;

class SendCampaignControllerTest extends TestCase
{
    private Campaign $campaign;

    public function setUp(): void
    {
        parent::setUp();

        $this->authenticate();

        $this->campaign = Campaign::factory()->create([
            'status' => CampaignStatus::DRAFT,
        ]);

        Bus::fake();
    }

    /** @test */
    public function it_can_send_a_campaign()
    {
        $this
            ->post(action(SendCampaignController::class, $this->campaign->id))
            ->assertRedirect(action(CampaignSummaryController::class, $this->campaign->id));

        Bus::assertDispatched(SendCampaignJob::class);
    }
}
