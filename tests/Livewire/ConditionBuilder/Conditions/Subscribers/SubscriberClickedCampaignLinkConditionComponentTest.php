<?php

namespace Spatie\Mailcoach\Tests\Livewire\ConditionBuilder\Conditions\Subscribers;

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Content\Models\Link;
use Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers\SubscriberClickedCampaignLinkConditionComponent;

beforeEach(function () {
});

it('can start with an empty condition collection', function () {
    $campaigns = Campaign::factory()
        ->has(ContentItem::factory()->has(Link::factory(), 'links'), 'contentItem')
        ->create(['name' => fake()->words(3, true)]);

    Livewire::test(SubscriberClickedCampaignLinkConditionComponent::class, ['storedCondition' => emptyStoredCondition()])
        ->assertHasNoErrors()
        ->assertSet('campaigns', $campaigns->pluck('name', 'id')->toArray())
        ->assertSet('storedCondition', emptyStoredCondition());
});

it('can add a condition', function () {
    $campaign = Campaign::factory()
        ->has(ContentItem::factory()->has(Link::factory(), 'links'), 'contentItem')
        ->create(['name' => fake()->words(3, true)]);

    Livewire::test(SubscriberClickedCampaignLinkConditionComponent::class, ['storedCondition' => emptyStoredCondition()])
        ->set('campaignId', $campaign->id)
        ->set('storedCondition', emptyStoredCondition(comparison: 'any'))
        ->assertHasNoErrors();
});
