<?php

namespace Spatie\Mailcoach\Tests\Livewire\ConditionBuilder;

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberClickedCampaignLinkQueryCondition;
use Spatie\Mailcoach\Livewire\ConditionBuilder\ConditionBuilderComponent;

it('can initialize a component', function () {
    Livewire::test(ConditionBuilderComponent::class)->assertHasNoErrors();
});

it('can initialize an empty condition', function () {
    Livewire::test(ConditionBuilderComponent::class)
        ->call('add', SubscriberClickedCampaignLinkQueryCondition::KEY)
        ->assertSet('storedConditions', [
            [
                'condition' => [
                    'key' => 'subscriber_clicked_campaign_link',
                    'label' => 'Subscriber Clicked Campaign Link',
                    'comparison_operators' => [
                        'any' => 'Contains Any',
                        'none' => 'Contains None',
                        'equals' => 'Equals To',
                        'not-equals' => 'Not Equals To',
                    ],
                    'data' => [],
                ],
                'comparison_operator' => null,
                'value' => [],
            ],
        ]);
});

it('can store a condition', function () {
    Livewire::test(ConditionBuilderComponent::class)
        ->call('add', SubscriberClickedCampaignLinkQueryCondition::KEY)
        ->set('storedConditions.0.comparison_operator', 'any');
});

it('can load segment stored conditions', function () {
})->skip('TODO');
