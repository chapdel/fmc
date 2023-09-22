<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\Campaigns;

use Spatie\Mailcoach\Database\Factories\SendFeedbackItemFactory;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignBouncesController;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

it('can get subscribers with bounces of a campaign', function () {
    test()->loginToApi();

    /** @var Campaign $campaign */
    $campaign = CampaignFactory::new()->create();

    Send::factory()
        ->has(SendFeedbackItemFactory::times(2), 'feedback')
        ->create(['content_item_id' => $campaign->contentItem->id]);

    Send::factory()
        ->has(SendFeedbackItemFactory::times(2), 'feedback')
        ->create(['content_item_id' => $campaign->contentItem->id]);

    /** @var Subscriber $subscriber */
    $subscriber = $campaign->contentItem->sends->first()->subscriber;

    $this
        ->getJson(action(CampaignBouncesController::class, $campaign))
        ->assertSuccessful()
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment([
            'subscriber_uuid' => $subscriber->uuid,
            'subscriber_email' => $subscriber->email,
            'subscriber_email_list_uuid' => $subscriber->emailList->uuid,
            'type' => 'bounce',
            'bounce_count' => 2,
        ]);
});

it('can filter by type', function () {
    test()->loginToApi();

    /** @var Campaign $campaign */
    $campaign = CampaignFactory::new()->create();

    Send::factory()
        ->has(SendFeedbackItemFactory::times(1), 'feedback')
        ->create(['content_item_id' => $campaign->contentItem->id]);

    Send::factory()
        ->has(SendFeedbackItemFactory::new()->state([
            'type' => 'complaint',
        ]), 'feedback')
        ->create(['content_item_id' => $campaign->contentItem->id]);

    /** @var Subscriber $subscriber */
    $subscriber = $campaign->contentItem->sends->last()->subscriber;

    $this
        ->getJson(action(CampaignBouncesController::class, $campaign).'?filter[type]=complaint')
        ->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment([
            'subscriber_uuid' => $subscriber->uuid,
            'subscriber_email' => $subscriber->email,
            'subscriber_email_list_uuid' => $subscriber->emailList->uuid,
            'type' => 'complaint',
            'bounce_count' => 1,
        ]);
});
