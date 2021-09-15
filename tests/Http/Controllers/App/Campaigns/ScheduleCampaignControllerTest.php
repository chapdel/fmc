<?php

use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignDeliveryController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\ScheduleCampaignController;
use Spatie\TestTime\TestTime;

beforeEach(function () {
    test()->authenticate();

    test()->campaign = Campaign::factory()->create([
        'status' => CampaignStatus::DRAFT,
    ]);

    TestTime::freeze('Y-m-d H:i:s', '2019-01-01 00:00:00');
});

it('can schedule a campaign', function () {
    $scheduleAt = now()->addDay();

    $this
        ->post(
            action(ScheduleCampaignController::class, test()->campaign),
            [
                'scheduled_at' => [
                    'date' => $scheduleAt->format('Y-m-d'),
                    'hours' => $scheduleAt->format('H'),
                    'minutes' => $scheduleAt->format('i'),
                ],
            ]
        )
        ->assertSessionHasNoErrors()
        ->assertRedirect(action(CampaignDeliveryController::class, test()->campaign->id));

    expect(test()->campaign->refresh()->scheduled_at->format('Y-m-d H:i:s'))->toEqual($scheduleAt->format('Y-m-d H:i:s'));
});

it('will not schedule a campaign in the past', function () {
    test()->withExceptionHandling();

    $scheduleAt = '2018-01-01 00:00:00';

    $this
        ->post(
            action(ScheduleCampaignController::class, test()->campaign),
            ['scheduled_at' => $scheduleAt]
        )
        ->assertSessionHasErrors('scheduled_at');
});
