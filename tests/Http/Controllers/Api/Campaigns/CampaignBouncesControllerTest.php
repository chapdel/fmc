<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\Campaigns;

use Illuminate\Auth\AuthenticationException;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Database\Factories\SendFeedbackItemFactory;
use Spatie\Mailcoach\Database\Factories\SubscriberFactory;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignBouncesController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

it('can get subscribers with bounces of a campaign', function () {
    test()->loginToApi();

    /** @var Campaign $campaign */
    $campaign = Campaign::factory()
        ->has(SendFactory::new()
            ->has(SendFeedbackItemFactory::times(2), 'feedback')
            ->has(SubscriberFactory::new())
        )
        ->has(SendFactory::new()
            ->has(SendFeedbackItemFactory::new(), 'feedback')
            ->has(SubscriberFactory::new())
        )
        ->create();

    /** @var Subscriber $subscriber */
    $subscriber = $campaign->sends->first()->subscriber;

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
    $campaign = Campaign::factory()
        ->has(SendFactory::new()
            ->has(SendFeedbackItemFactory::times(1), 'feedback')
            ->has(SubscriberFactory::new())
        )
        ->has(SendFactory::new()
            ->has(SendFeedbackItemFactory::new()->state([
                'type' => 'complaint',
            ]), 'feedback')
            ->has(SubscriberFactory::new())
        )
        ->create();

    /** @var Subscriber $subscriber */
    $subscriber = $campaign->sends->last()->subscriber;

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

it('cannot be accessed by a user without permissions', function () {
    $this->expectException(AuthenticationException::class);

    $this->getJson(action(CampaignBouncesController::class, 1));
});
