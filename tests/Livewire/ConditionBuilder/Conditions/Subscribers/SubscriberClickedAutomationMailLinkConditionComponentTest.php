<?php

namespace Spatie\Mailcoach\Tests\Livewire\ConditionBuilder\Conditions\Subscribers;

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberClickedCampaignLinkQueryCondition;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Content\Models\Link;
use Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers\SubscriberClickedAutomationMailLinkConditionComponent;

beforeEach(function () {
});

it('can start with an empty condition collection', function () {
    $models = AutomationMail::factory()
        ->has(ContentItem::factory()->has(Link::factory(), 'links'), 'contentItem')
        ->create(['name' => fake()->words(3, true)]);

    Livewire::test(SubscriberClickedAutomationMailLinkConditionComponent::class, ['storedCondition' => emptyStoredCondition()])
        ->assertHasNoErrors()
        ->assertSet('automationMails', $models->pluck('name', 'id')->toArray())
        ->assertSet('storedCondition', emptyStoredCondition());
});

it('can add a condition', function () {
    $model = AutomationMail::factory()
        ->has(ContentItem::factory()->has(Link::factory(), 'links'), 'contentItem')
        ->create(['name' => fake()->words(3, true)]);

    Livewire::test(SubscriberClickedAutomationMailLinkConditionComponent::class, ['storedCondition' => emptyStoredCondition()])
        ->set('automationMailId', $model->id)
        ->set('storedCondition', emptyStoredCondition(comparison: 'any'))
        ->assertHasNoErrors();
});

function emptyStoredCondition(string $comparison = null, mixed $value = null): array
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
        'comparison_operator' => $comparison,
        'value' => $value,
    ];
}
