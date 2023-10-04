<?php

namespace Spatie\Mailcoach\Tests\Livewire\ConditionBuilder\Conditions\Subscribers;

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberClickedCampaignLinkQueryCondition;
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
        //->call('add', SubscriberClickedCampaignLinkQueryCondition::KEY)
        ->assertHasNoErrors()
        ->assertSet('campaigns', $campaigns->pluck('name', 'id')->toArray())
        ->assertSet('storedCondition', emptyStoredCondition());
});

it('can add a condition', function () {
})->skip('TODO');

function emptyStoredCondition(): array
{
    return [
        'condition' => [
            'key' => SubscriberClickedCampaignLinkQueryCondition::KEY,
            'label' => 'Subscriber Clicked Automation Mail Link',
            'comparison_operators' => [
                'any' => 'Clicked Any Link',
                'none' => 'Did Not Click Any Link',
                'equals' => 'Clicked A Specific Link',
                'not-equals' => 'Did Not Click A Specific Link',
            ],
            'data' => [
                'campaignId' => null,
                'url' => null,
            ],
        ],
        'comparison_operator' => null,
        'value' => [],
    ];
}
