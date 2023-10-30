<?php

namespace Spatie\Mailcoach\Tests\Livewire\ConditionBuilder\Conditions\Subscribers;

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
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
        ->assertSet('storedCondition', emptyStoredCondition(value: ['automationMailId' => null, 'link' => null]));
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
