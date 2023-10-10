<?php

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberOpenedAutomationMailQueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

use function PHPUnit\Framework\assertTrue;

/** The key shouldn't change, this means db migrations */
it('has a key', function () {
    $condition = new SubscriberOpenedAutomationMailQueryCondition();

    expect($condition->key())->toBe('subscriber_opened_automation_mail');
});

it('can compare with an equals to operator', function () {
    $condition = new SubscriberOpenedAutomationMailQueryCondition();

    $subscriberA = Subscriber::factory()->create();

    $automationMail = AutomationMail::factory()
        ->state(['name' => 'campaign name'])
        ->create();

    $send = Send::factory()->create([
        'content_item_id' => $automationMail->contentItem->id,
        'subscriber_id' => $subscriberA->id,
    ]);

    $subscriberA->opens()->create([
        'content_item_id' => $automationMail->contentItem->id,
        'send_id' => $send->id,
    ]);

    $subscriberB = Subscriber::factory()->create();

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::Equals,
        value: $automationMail->id,
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::Equals,
        value: 502,
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));
});

it('can compare with an non equals to operator', function () {
    $condition = new SubscriberOpenedAutomationMailQueryCondition();

    $subscriberA = Subscriber::factory()->create();

    $automationMail = AutomationMail::factory()
        ->state(['name' => 'campaign name'])
        ->create();

    $send = Send::factory()->create([
        'content_item_id' => $automationMail->contentItem->id,
        'subscriber_id' => $subscriberA->id,
    ]);

    $subscriberA->opens()->create([
        'content_item_id' => $automationMail->contentItem->id,
        'send_id' => $send->id,
    ]);

    $subscriberB = Subscriber::factory()->create();

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::NotEquals,
        value: $automationMail->id,
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::NotEquals,
        value: 502,
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));
});

it('can compare with an any operator', function () {
    $condition = new SubscriberOpenedAutomationMailQueryCondition();

    $subscriberA = Subscriber::factory()->create();

    $automationMail = AutomationMail::factory()
        ->state(['name' => 'campaign name'])
        ->create();

    $send = Send::factory()->create([
        'content_item_id' => $automationMail->contentItem->id,
        'subscriber_id' => $subscriberA->id,
    ]);

    $subscriberA->opens()->create([
        'content_item_id' => $automationMail->contentItem->id,
        'send_id' => $send->id,
    ]);

    $subscriberB = Subscriber::factory()->create();

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::Any,
        value: $automationMail->id,
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

    $automationMail = AutomationMail::factory()
        ->state(['name' => 'campaign name'])
        ->create();

    $send = Send::factory()->create([
        'content_item_id' => $automationMail->contentItem->id,
        'subscriber_id' => $subscriberA->id,
    ]);

    $subscriberA->opens()->create([
        'content_item_id' => $automationMail->contentItem->id,
        'send_id' => $send->id,
    ]);

    $subscriberB = Subscriber::factory()->create();

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::None,
        value: $automationMail->id,
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
