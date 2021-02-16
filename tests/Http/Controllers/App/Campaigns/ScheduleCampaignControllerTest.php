<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\App\Campaigns;

use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignDeliveryController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\ScheduleCampaignController;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class ScheduleCampaignControllerTest extends TestCase
{
    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign */
    protected $campaign;

    public function setUp(): void
    {
        parent::setUp();

        $this->authenticate();

        $this->campaign = Campaign::factory()->create([
            'status' => CampaignStatus::DRAFT,
        ]);

        TestTime::freeze('Y-m-d H:i:s', '2019-01-01 00:00:00');
    }

    /** @test */
    public function it_can_schedule_a_campaign()
    {
        $scheduleAt = now()->addDay();

        $this
            ->post(
                action(ScheduleCampaignController::class, $this->campaign),
                [
                    'scheduled_at' => [
                        'date' => $scheduleAt->format('Y-m-d'),
                        'hours' => $scheduleAt->format('H'),
                        'minutes' => $scheduleAt->format('i'),
                    ],
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(action(CampaignDeliveryController::class, $this->campaign->id));

        $this->assertEquals($scheduleAt->format('Y-m-d H:i:s'), $this->campaign->refresh()->scheduled_at->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_will_not_schedule_a_campaign_in_the_past()
    {
        $this->withExceptionHandling();

        $scheduleAt = '2018-01-01 00:00:00';

        $this
            ->post(
                action(ScheduleCampaignController::class, $this->campaign),
                ['scheduled_at' => $scheduleAt]
            )
            ->assertSessionHasErrors('scheduled_at');
    }
}
