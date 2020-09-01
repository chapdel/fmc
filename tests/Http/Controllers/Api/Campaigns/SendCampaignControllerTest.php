<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\Campaigns;

use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Enums\CampaignStatus;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\SendCampaignController;
use Spatie\Mailcoach\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class SendCampaignControllerTest extends TestCase
{
    use RespondsToApiRequests;

    private Campaign $campaign;

    public function setUp(): void
    {
        parent::setUp();

        Bus::fake();

        $this->loginToApi();

        $this->campaign = Campaign::factory()->create([
            'status' => CampaignStatus::DRAFT,
        ]);
    }

    /** @test */
    public function a_campaign_can_be_sent_using_the_api()
    {
        $this
            ->postJson(action(SendCampaignController::class, $this->campaign))
            ->assertSuccessful();

        Bus::assertDispatched(function (SendCampaignJob $job) {
            $this->assertEquals($this->campaign->id, $job->campaign->id);

            return true;
        });
    }

    /** @test */
    public function it_will_not_send_a_campaign_that_has_already_been_sent()
    {
        $this->withExceptionHandling();

        $this->campaign->update(['status' => CampaignStatus::SENT]);

        $this
            ->postJson(action(SendCampaignController::class, $this->campaign))
            ->assertJsonValidationErrors('campaign');

        Bus::assertNotDispatched(SendCampaignJob::class);
    }
}
