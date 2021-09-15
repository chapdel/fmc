<?php

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\HaltAction;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

it('halts the automation', function () {
    $action = new HaltAction();

    test()->assertTrue($action->shouldHalt(Subscriber::factory()->create()));
});

it('wont continue', function () {
    $action = new HaltAction();

    test()->assertFalse($action->shouldContinue(Subscriber::factory()->create()));
});
