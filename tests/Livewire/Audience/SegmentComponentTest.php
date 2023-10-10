<?php

namespace Spatie\Mailcoach\Tests\Livewire\Audience;

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Livewire\Audience\SegmentComponent;
use Spatie\Mailcoach\MainNavigation;

beforeEach(function () {
    test()->authenticate();
});

it('can store the stored conditions', function () {
    /** @var ContentItem $contentItem */
    $contentItem = ContentItem::factory()->create();
    $contentItem->model()->associate(Campaign::factory()->create())->save();
    $contentItem->links()->createMany([
        ['url' => 'https://spatie.be'],
        ['url' => 'https://spatie.be/open-source'],
    ]);
    $contentItem->refresh();

    /** @var TagSegment $segment */
    $segment = TagSegment::factory()->create();
    $segment->campaigns()->save($contentItem->model);
    $segment->refresh();

    $storedCondition = [
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
            ],
            'comparison_operator' => 'any',
            'value' => [
                'url' => $segment->campaigns->first()->contentItem->links->first()->url,
                'campaignId' => $segment->campaigns->first()->id,
            ],
        ],
    ];

    Livewire::test(SegmentComponent::class, [
        'emailList' => $segment->emailList,
        'segment' => $segment,
        'mainNavigation' => MainNavigation::make(),
    ])
        ->assertHasNoErrors()
        ->call('updateStoredConditions', $storedCondition)
        ->call('save')
        ->assertHasNoErrors();

    $segment->refresh();

    expect($segment->stored_conditions->first()->toArray())->toBe($storedCondition[0]);
});
