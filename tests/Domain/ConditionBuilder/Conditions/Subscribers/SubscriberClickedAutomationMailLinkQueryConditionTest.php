<?php

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberClickedAutomationMailLinkQueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Data\SubscriberClickedAutomationMailLinkQueryConditionData;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

use function PHPUnit\Framework\assertTrue;

/** The key shouldn't change, this means db migrations */
it('has a key', function () {
    $condition = new SubscriberClickedAutomationMailLinkQueryCondition();

    expect($condition->key())->toBe('subscriber_clicked_automation_mail_link');
});

it('can compare with an equals to operator', function () {
    $condition = new SubscriberClickedAutomationMailLinkQueryCondition();

    $automationMail = AutomationMail::factory()->create();

    $subscriberA = Subscriber::factory()
        ->has(Send::factory()->state(['content_item_id' => $automationMail->contentItem->id]), 'sends')
        ->create();

    $subscriberA->sends->first()->registerClick('https://spatie.be');

    $subscriberB = Subscriber::factory()->create();

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::Equals,
        value: SubscriberClickedAutomationMailLinkQueryConditionData::make($automationMail->id, 'https://spatie.be'),
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::Equals,
        value: SubscriberClickedAutomationMailLinkQueryConditionData::make($automationMail->id, 'https://unknown.be'),
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));
});

it('can compare with an non equals to operator', function () {
    $condition = new SubscriberClickedAutomationMailLinkQueryCondition();

    $automationMail = AutomationMail::factory()
        ->create();

    $subscriberA = Subscriber::factory()
        ->has(Send::factory()->state(['content_item_id' => $automationMail->contentItem->id]), 'sends')
        ->create();

    $subscriberA->sends->first()->registerClick('https://spatie.be');

    $subscriberB = Subscriber::factory()->create();

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::NotEquals,
        value: SubscriberClickedAutomationMailLinkQueryConditionData::make($automationMail->id, 'https://spatie.be'),
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::NotEquals,
        value: SubscriberClickedAutomationMailLinkQueryConditionData::make($automationMail->id, 'https://unknown.be'),
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));
});

it('can compare with an any operator', function () {
    $condition = new SubscriberClickedAutomationMailLinkQueryCondition();

    $automationMail = AutomationMail::factory()
        ->create();

    $subscriberA = Subscriber::factory()
        ->has(Send::factory()->state(['content_item_id' => $automationMail->contentItem->id]), 'sends')
        ->create();

    $subscriberA->sends->first()->registerClick('https://spatie.be');

    $subscriberB = Subscriber::factory()->create();

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::Any,
        value: SubscriberClickedAutomationMailLinkQueryConditionData::make($automationMail->id),
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::Any,
        value: SubscriberClickedAutomationMailLinkQueryConditionData::make($automationMail->id),
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));
});

it('can compare with a none operator', function () {
    $condition = new SubscriberClickedAutomationMailLinkQueryCondition();

    $automationMail = AutomationMail::factory()
        ->create();

    $subscriberA = Subscriber::factory()
        ->has(Send::factory()->state(['content_item_id' => $automationMail->contentItem->id]), 'sends')
        ->create();

    $subscriberA->sends->first()->registerClick('https://spatie.be');

    $subscriberB = Subscriber::factory()->create();

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::None,
        value: SubscriberClickedAutomationMailLinkQueryConditionData::make($automationMail->id),
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::None,
        value: SubscriberClickedAutomationMailLinkQueryConditionData::make($automationMail->id),
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));
});

it('cannot use a non supported operator', function () {
    $condition = new SubscriberClickedAutomationMailLinkQueryCondition();

    $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::In,
        value: SubscriberClickedAutomationMailLinkQueryConditionData::make(20, 'niels'),
    );
})->throws('Operator `in` is not allowed for condition `Subscriber Clicked Automation Mail Link`.');
