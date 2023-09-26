<?php

use Spatie\Mailcoach\Domain\ConditionBuilder\Collections\StoredConditionCollection;

use function PHPUnit\Framework\assertInstanceOf;

it('can create a collection', function () {
    $collection = StoredConditionCollection::fromRequest([
        [
            'condition' => [
                'key' => 'subscriber_tags',
            ],
            'comparison_operator' => 'in',
            'value' => 'flare',
        ],
        [
            'condition' => [
                'key' => 'subscriber_tags',
            ],
            'comparison_operator' => 'not-in',
            'value' => 'mailcoach',
        ],
    ]);

    assertInstanceOf(StoredConditionCollection::class, $collection);
    expect($collection->count())->toBe(2);
});
