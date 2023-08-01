<?php

namespace Spatie\Mailcoach\Tests\Domain\ConditionBuilder\Conditions\Subscribers;

use function PHPUnit\Framework\assertTrue;
use Spatie\Mailcoach\Database\Factories\CampaignFactory;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberOpenedAutomationMailQueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

/** The key shouldn't change, this means db migrations */
it('has a key', function () {
    $condition = new SubscriberOpenedAutomationMailQueryCondition();

    expect($condition->key())->toBe('subscriber_opened_automation_mail');
});

it('can compare with an equals to operator', function () {
    $condition = new SubscriberOpenedAutomationMailQueryCondition();

    $subscriberA = Subscriber::factory()->create();

    $campaign = CampaignFactory::new()
        ->state(['name' => 'campaign name'])
        ->has(Send::factory()->state(['subscriber_id' => $subscriberA->id]))
        ->create();

    $subscriberA->automationMailOpens()->create([
        'send_id' => $campaign->sends->first()->id,
    ]);

    $subscriberB = Subscriber::factory()->create();

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::Equals,
        value: $campaign->name,
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::Equals,
        value: 'unknown campaign name',
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));
});

it('can compare with an non equals to operator', function () {
    $condition = new SubscriberOpenedAutomationMailQueryCondition();

    $subscriberA = Subscriber::factory()->create();

    $campaign = CampaignFactory::new()
        ->state(['name' => 'campaign name'])
        ->has(Send::factory()->state(['subscriber_id' => $subscriberA->id]))
        ->create();

    $subscriberA->automationMailOpens()->create([
        'send_id' => $campaign->sends->first()->id,
    ]);

    $subscriberB = Subscriber::factory()->create();

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::NotEquals,
        value: $campaign->name,
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::NotEquals,
        value: 'unknown campaign name',
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));
});

it('can compare with an any operator', function () {
    $condition = new SubscriberOpenedAutomationMailQueryCondition();

    $subscriberA = Subscriber::factory()->create();

    $campaign = CampaignFactory::new()
        ->state(['name' => 'campaign name'])
        ->has(Send::factory()->state(['subscriber_id' => $subscriberA->id]))
        ->create();

    $subscriberA->automationMailOpens()->create([
        'send_id' => $campaign->sends->first()->id,
    ]);

    $subscriberB = Subscriber::factory()->create();

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::Any,
        value: $campaign->name,
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::Any,
        value: 'unknown campaign name',
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));
});

it('can compare with a none operator', function () {
    $condition = new SubscriberOpenedAutomationMailQueryCondition();

    $subscriberA = Subscriber::factory()->create();

    $campaign = CampaignFactory::new()
        ->state(['name' => 'campaign name'])
        ->has(Send::factory()->state(['subscriber_id' => $subscriberA->id]))
        ->create();

    $subscriberA->automationMailOpens()->create([
        'send_id' => $campaign->sends->first()->id,
    ]);

    $subscriberB = Subscriber::factory()->create();

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::None,
        value: $campaign->name,
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::None,
        value: 'unknown campaign name',
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));
});

it('cannot use a non supported operator', function () {
    $condition = new SubscriberOpenedAutomationMailQueryCondition();

    $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::In,
        value: 'tagA',
    );
})->throws('Operator `in` is not allowed for condition `Subscriber Opened Automation Mail`.');
