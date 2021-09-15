<?php

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasTagCondition;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

it('checks for a tag', function () {
    $automation = Automation::factory()->create();
    $subscriber = Subscriber::factory()->create();

    $condition = new HasTagCondition($automation, $subscriber, [
        'tag' => 'some-tag',
    ]);

    test()->assertFalse($condition->check());

    $subscriber->addTag('some-tag');

    test()->assertTrue($condition->check());
});
