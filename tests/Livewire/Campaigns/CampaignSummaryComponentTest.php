<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

beforeEach(function () {
    $this->authenticate();
});

it('redirects to delivery when a campaign is draft', function () {
    $campaign = Campaign::factory()->create([
        'status' => CampaignStatus::Draft,
    ]);

    Livewire::test('mailcoach::campaign-summary', ['campaign' => $campaign])
        ->assertRedirect(route('mailcoach.campaigns.delivery', $campaign));
});

it('can cancel sending a campaign', function () {
    $campaign = Campaign::factory()->has(ContentItem::factory())->create([
        'status' => CampaignStatus::Sending,
    ]);

    Livewire::test('mailcoach::campaign-summary', ['campaign' => $campaign])
        ->call('cancelSending');

    expect($campaign->fresh()->status)->toBe(CampaignStatus::Cancelled);
});

it('shows preparing while there are no sends yet', function () {
    $campaign = Campaign::factory()->create([
        'status' => CampaignStatus::Sending,
    ]);

    $campaign->contentItem->update([
        'sent_to_number_of_subscribers' => 0,
    ]);

    Livewire::test('mailcoach::campaign-summary', ['campaign' => $campaign])
        ->assertSee('preparing to send');
});

it('shows sends count when cancelled', function () {
    $campaign = Campaign::factory()->create([
        'status' => CampaignStatus::Cancelled,
    ]);

    $campaign->contentItem->update([
        'sent_to_number_of_subscribers' => 100,
    ]);

    Send::factory()->create([
        'content_item_id' => $campaign->contentItem->id,
        'sent_at' => now(),
    ]);

    Livewire::test('mailcoach::campaign-summary', ['campaign' => $campaign])
        ->assertSee('sending is cancelled')
        ->assertSee('It was sent to 1/100');
});

it('shows progress while sending and creating sends', function () {
    $campaign = Campaign::factory()->create([
        'status' => CampaignStatus::Sending,
    ]);

    $campaign->contentItem->update([
        'sent_to_number_of_subscribers' => 100,
    ]);

    Send::factory()->create([
        'content_item_id' => $campaign->contentItem->id,
        'sent_at' => now(),
    ]);

    Livewire::test('mailcoach::campaign-summary', ['campaign' => $campaign])
        ->assertSee('1%');
});

it('shows progress while sending and all sends are created sends', function () {
    $campaign = Campaign::factory()->create([
        'status' => CampaignStatus::Sending,
    ]);

    $campaign->contentItem->update([
        'sent_to_number_of_subscribers' => 1,
    ]);

    Send::factory()->create([
        'content_item_id' => $campaign->contentItem->id,
        'sent_at' => null,
    ]);

    Livewire::test('mailcoach::campaign-summary', ['campaign' => $campaign])
        ->assertSee('50%');
});

it('shows results when campaign is sent', function () {
    $campaign = Campaign::factory()->create([
        'status' => CampaignStatus::Sent,
        'sent_at' => now(),
    ]);

    $campaign->contentItem->update([
        'sent_to_number_of_subscribers' => 1,
    ]);

    Send::factory()->create([
        'content_item_id' => $campaign->contentItem->id,
        'sent_at' => now(),
    ]);

    Livewire::test('mailcoach::campaign-summary', ['campaign' => $campaign])
        ->assertSee('was delivered successfully')
        ->assertSee('1 subscriber');
});

it('does not count invalidated sends', function () {
    $campaign = Campaign::factory()->create([
        'status' => CampaignStatus::Sent,
        'sent_at' => now(),
    ]);

    $campaign->contentItem->update([
        'sent_to_number_of_subscribers' => 2,
    ]);

    Send::factory()->create([
        'content_item_id' => $campaign->contentItem->id,
        'sent_at' => now(),
    ]);

    Send::factory()->create([
        'content_item_id' => $campaign->contentItem->id,
        'sent_at' => now(),
        'invalidated_at' => now(),
    ]);

    Livewire::test('mailcoach::campaign-summary', ['campaign' => $campaign])
        ->assertSee('was delivered successfully')
        ->assertSee('1 subscriber');
});
