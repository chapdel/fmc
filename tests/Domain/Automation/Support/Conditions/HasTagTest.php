<?php

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasTagCondition;

it('checks for a tag', function () {
    $automation = Automation::factory()->create();
    $subscriber = Subscriber::factory()->create();

    $condition = new HasTagCondition($automation, $subscriber, [
        'tag' => 'some-tag',
    ]);

    expect($condition->check())->toBeFalse();

    $subscriber->addTag('some-tag');

    expect($condition->check())->toBeTrue();
});
