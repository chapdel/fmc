<?php

use Spatie\Mailcoach\Database\Factories\CampaignFactory;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberClickedCampaignLinkQueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

use function PHPUnit\Framework\assertTrue;

/** The key shouldn't change, this means db migrations */
it('has a key', function () {
    $condition = new SubscriberClickedCampaignLinkQueryCondition();

    expect($condition->key())->toBe('subscriber_clicked_campaign_link');
});

it('can compare with an equals to operator', function () {
    $condition = new SubscriberClickedCampaignLinkQueryCondition();

    /** @var Campaign $campaign */
    $campaign = CampaignFactory::new()->create();

    $subscriberA = Subscriber::factory()
        ->has(Send::factory()->state(['content_item_id' => $campaign->contentItem->id]), 'sends')
        ->create();

    $subscriberA->sends->first()->registerClick('https://spatie.be');

    $subscriberB = Subscriber::factory()->create();

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::Equals,
        value: ['campaignId' => $campaign->id, 'link' => 'https://spatie.be'],
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::Equals,
        value: ['campaignId' => $campaign->id, 'link' => 'https://unknown.be'],
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));
});

it('can compare with an non equals to operator', function () {
    $condition = new SubscriberClickedCampaignLinkQueryCondition();

    /** @var Campaign $campaign */
    $campaign = CampaignFactory::new()->create();

    $subscriberA = Subscriber::factory()
        ->has(Send::factory()->state(['content_item_id' => $campaign->contentItem->id]), 'sends')
        ->create();

    $subscriberA->sends->first()->registerClick('https://spatie.be');

    $subscriberB = Subscriber::factory()->create();

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::NotEquals,
        value: ['campaignId' => $campaign->id, 'link' => 'https://spatie.be'],
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::NotEquals,
        value: ['campaignId' => $campaign->id, 'link' => 'https://unknown.be'],
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));
});

it('can compare with an any operator', function () {
    $condition = new SubscriberClickedCampaignLinkQueryCondition();

    /** @var Campaign $campaign */
    $campaign = CampaignFactory::new()->create();

    $subscriberA = Subscriber::factory()
        ->has(Send::factory()->state(['content_item_id' => $campaign->contentItem->id]), 'sends')
        ->create();

    $subscriberA->sends->first()->registerClick('https://spatie.be');

    $subscriberB = Subscriber::factory()->create();

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::Any,
        value: ['campaignId' => $campaign->id],
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::Any,
        value: ['campaignId' => $campaign->id],
    );

    assertTrue($query->pluck('id')->contains($subscriberA->id));
    assertTrue($query->pluck('id')->doesntContain($subscriberB->id));
});

it('can compare with a none operator', function () {
    $condition = new SubscriberClickedCampaignLinkQueryCondition();

    /** @var Campaign $campaign */
    $campaign = CampaignFactory::new()->create();

    $subscriberA = Subscriber::factory()
        ->has(Send::factory()->state(['content_item_id' => $campaign->contentItem->id]), 'sends')
        ->create();

    $subscriberA->sends->first()->registerClick('https://spatie.be');

    $subscriberB = Subscriber::factory()->create();

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::None,
        value: ['campaignId' => $campaign->id],
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));

    $query = $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::None,
        value: ['campaignId' => $campaign->id],
    );

    assertTrue($query->pluck('id')->doesntContain($subscriberA->id));
    assertTrue($query->pluck('id')->contains($subscriberB->id));
});

it('cannot use a non supported operator', function () {
    $condition = new SubscriberClickedCampaignLinkQueryCondition();

    $condition->apply(
        baseQuery: Subscriber::query(),
        operator: ComparisonOperator::In,
        value: ['campaignId' => 1, 'link' => 'https://spatie.be'],
    );
})->throws('Operator `in` is not allowed for condition `Subscriber Clicked Campaign Link`.');
