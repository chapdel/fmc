<?php

namespace Spatie\Mailcoach\Tests\Features\Controllers\App\Campaigns;

use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignSettingsController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CreateCampaignController;
use Spatie\Mailcoach\Tests\TestCase;

class CreateCampaignControllerTest extends TestCase
{
    /** @test */
    public function it_can_create_a_campaign()
    {
        $this->authenticate();

        $this
            ->post(action(CreateCampaignController::class), ['name' => 'my campaign', 'type' => CampaignStatus::DRAFT])
            ->assertRedirect(action([CampaignSettingsController::class, 'edit'], 1));

        $this->assertDatabaseHas('mailcoach_campaigns', ['name' => 'my campaign']);
    }
}
