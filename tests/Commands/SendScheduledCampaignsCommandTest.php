<?php

namespace Tests\Feature\Commands;

use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Commands\SendScheduledCampaignsCommand;
use Spatie\Mailcoach\Enums\CampaignStatus;
use Spatie\Mailcoach\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class SendScheduledCampaignsCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        TestTime::freeze();

        Bus::fake();
    }

    /** @test */
    public function it_will_not_send_campaigns_that_are_not_scheduled()
    {
        Campaign::factory()->create([
            'scheduled_at' => null,
            'status' => CampaignStatus::DRAFT,
        ]);

        $this->artisan(SendScheduledCampaignsCommand::class)->assertExitCode(0);

        Bus::assertNotDispatched(SendCampaignJob::class);
    }

    /** @test */
    public function it_will_not_send_a_campaign_that_has_a_scheduled_at_in_the_future()
    {
        Campaign::factory()->create([
            'scheduled_at' => now()->addSecond(),
            'status' => CampaignStatus::DRAFT,
        ]);

        $this->artisan(SendScheduledCampaignsCommand::class)->assertExitCode(0);

        Bus::assertNotDispatched(SendCampaignJob::class);
    }

    /** @test */
    public function it_will_send_a_campaign_that_has_a_scheduled_at_set_to_in_the_past()
    {
        Campaign::factory()->create([
            'scheduled_at' => now()->subSecond(),
            'status' => CampaignStatus::DRAFT,
        ]);

        $this->artisan(SendScheduledCampaignsCommand::class)->assertExitCode(0);

        Bus::assertDispatched(SendCampaignJob::class);
    }

    /** @test */
    public function it_will_not_send_a_campaign_twice()
    {
        Campaign::factory()->create([
            'scheduled_at' => now()->subSecond(),
            'status' => CampaignStatus::DRAFT,
        ]);

        $this->artisan(SendScheduledCampaignsCommand::class)->assertExitCode(0);
        Bus::assertDispatchedTimes(SendCampaignJob::class, 1);

        $this->artisan(SendScheduledCampaignsCommand::class)->assertExitCode(0);
        Bus::assertDispatchedTimes(SendCampaignJob::class, 1);
    }
}
