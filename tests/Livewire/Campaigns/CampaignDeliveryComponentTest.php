<?php

use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignDeliveryComponent;
use Spatie\TestTime\TestTime;

beforeEach(function () {
    $this->authenticate();

    $this->campaign = Campaign::factory()->create([
        'status' => CampaignStatus::Draft,
    ]);

    TestTime::freeze('Y-m-d H:i:s', '2019-01-01 00:00:00');
    Bus::fake();
});

it('can schedule a campaign', function () {
    $scheduleAt = now()->addDay();

    \Livewire\Livewire::test(CampaignDeliveryComponent::class, ['campaign' => $this->campaign])
        ->set('scheduled_at', [
            'date' => $scheduleAt->format('Y-m-d'),
            'hours' => $scheduleAt->format('H'),
            'minutes' => $scheduleAt->format('i'),
        ])
        ->call('schedule')
        ->assertHasNoErrors();

    expect($this->campaign->refresh()->scheduled_at->format('Y-m-d H:i:s'))->toEqual($scheduleAt->format('Y-m-d H:i:s'));
});

it('will not schedule a campaign in the past', function () {
    $this->withExceptionHandling();

    $scheduleAt = \Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i:s', '2018-01-01 00:00:00');

    \Livewire\Livewire::test(CampaignDeliveryComponent::class, ['campaign' => $this->campaign])
        ->set('scheduled_at', [
            'date' => $scheduleAt->format('Y-m-d'),
            'hours' => $scheduleAt->format('H'),
            'minutes' => $scheduleAt->format('i'),
        ])
        ->call('schedule')
        ->assertHasErrors('scheduled_at');
});

it('can unschedule a campaign', function () {
    test()->authenticate();

    $campaign = Campaign::factory()->create([
        'scheduled_at' => now()->format('Y-m-d H:i:s'),
    ]);

    expect($campaign->refresh()->scheduled_at)->not()->toBeNull();

    \Livewire\Livewire::test(CampaignDeliveryComponent::class, ['campaign' => $campaign])
        ->call('unschedule');

    expect($campaign->refresh()->scheduled_at)->toBeNull();
});

it('can send a campaign', function () {
    \Livewire\Livewire::test(CampaignDeliveryComponent::class, ['campaign' => $this->campaign])
        ->call('send');

    expect(test()->campaign->fresh()->status)->toBe(CampaignStatus::Sending);
});
