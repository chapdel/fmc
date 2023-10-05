<?php

namespace Spatie\Mailcoach\Tests\Livewire\Audience;

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Livewire\Audience\SegmentComponent;
use Spatie\Mailcoach\MainNavigation;

beforeEach(function () {
    test()->authenticate();
});

it('can store the stored conditions', function () {
    $segment = TagSegment::factory()->create();

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
                'data' => [],
            ],
            'comparison_operator' => 'any',
            'value' => [],
        ],
    ];

    Livewire::test(SegmentComponent::class, [
        'emailList' => $segment->emailList,
        'segment' => $segment,
        'mainNavigation' => MainNavigation::make(),
    ])
        ->assertHasNoErrors()
        ->call('updateStoredConditions', $storedCondition)
        ->call('save');

    $segment->refresh();

    expect($segment->stored_conditions->first()->toArray())->toBe($storedCondition[0]);
});
