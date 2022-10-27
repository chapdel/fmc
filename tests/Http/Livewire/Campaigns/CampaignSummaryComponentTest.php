<?php

use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignDeliveryComponent;
use Spatie\TestTime\TestTime;

beforeEach(function () {
    $this->authenticate();
});

it('redirects to delivery when a campaign is draft', function () {
    $campaign = Campaign::factory()->create([
        'status' => CampaignStatus::Draft,
    ]);

    \Livewire\Livewire::test('mailcoach::campaign-summary', ['campaign' => $campaign])
        ->assertRedirect(route('mailcoach.campaigns.delivery', $campaign));
});

it('can cancel sending a campaign', function () {
    $campaign = Campaign::factory()->create([
        'status' => CampaignStatus::Sending,
    ]);

    \Livewire\Livewire::test('mailcoach::campaign-summary', ['campaign' => $campaign])
        ->call('cancelSending');

    expect($campaign->fresh()->status)->toBe(CampaignStatus::Cancelled);
});

it('shows preparing while there are no sends yet', function () {
    $campaign = Campaign::factory()->create([
        'status' => CampaignStatus::Sending,
        'sent_to_number_of_subscribers' => 0,
    ]);

    \Livewire\Livewire::test('mailcoach::campaign-summary', ['campaign' => $campaign])
        ->assertSee('preparing to send');
});

it('shows sends count when cancelled', function () {
    $campaign = Campaign::factory()->create([
        'status' => CampaignStatus::Cancelled,
        'sent_to_number_of_subscribers' => 100,
    ]);

    Send::factory()->create([
        'campaign_id' => $campaign->id,
        'sent_at' => now(),
    ]);

    \Livewire\Livewire::test('mailcoach::campaign-summary', ['campaign' => $campaign])
        ->assertSee('sending is cancelled')
        ->assertSee('It was sent to 1/100');
});

it('shows progress while sending and creating sends', function () {
    $campaign = Campaign::factory()->create([
        'status' => CampaignStatus::Sending,
        'sent_to_number_of_subscribers' => 100,
    ]);

    Send::factory()->create([
        'campaign_id' => $campaign->id,
        'sent_at' => now(),
    ]);

    \Livewire\Livewire::test('mailcoach::campaign-summary', ['campaign' => $campaign])
        ->assertSee('is preparing 1/100 sends');
});

it('shows progress while sending and all sends are created sends', function () {
    $campaign = Campaign::factory()->create([
        'status' => CampaignStatus::Sending,
        'sent_to_number_of_subscribers' => 1,
    ]);

    Send::factory()->create([
        'campaign_id' => $campaign->id,
        'sent_at' => null,
    ]);

    \Livewire\Livewire::test('mailcoach::campaign-summary', ['campaign' => $campaign])
        ->assertSee('is sending to 0/1 subscriber');
});

it('shows results when campaign is sent', function () {
    $campaign = Campaign::factory()->create([
        'status' => CampaignStatus::Sent,
        'sent_to_number_of_subscribers' => 1,
        'sent_at' => now(),
    ]);

    Send::factory()->create([
        'campaign_id' => $campaign->id,
        'sent_at' => now(),
    ]);

    \Livewire\Livewire::test('mailcoach::campaign-summary', ['campaign' => $campaign])
        ->assertSee('was delivered successfully')
        ->assertSee('1 subscriber');
});

it('does not count invalidated sends', function () {
    $campaign = Campaign::factory()->create([
        'status' => CampaignStatus::Sent,
        'sent_to_number_of_subscribers' => 2,
        'sent_at' => now(),
    ]);

    Send::factory()->create([
        'campaign_id' => $campaign->id,
        'sent_at' => now(),
    ]);

    Send::factory()->create([
        'campaign_id' => $campaign->id,
        'sent_at' => now(),
        'invalidated_at' => now(),
    ]);

    \Livewire\Livewire::test('mailcoach::campaign-summary', ['campaign' => $campaign])
        ->assertSee('was delivered successfully')
        ->assertSee('1 subscriber');
});
