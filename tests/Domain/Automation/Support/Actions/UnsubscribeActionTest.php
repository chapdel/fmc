<?php

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\UnsubscribeAction;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

it('unsubscribes the subscriber', function () {
    $action = new UnsubscribeAction();

    /** @var Subscriber $subscriber */
    $subscriber = Subscriber::factory()->create();

    test()->assertTrue($subscriber->isSubscribed());

    $action->run($subscriber);

    test()->assertFalse($subscriber->fresh()->isSubscribed());
});

it('halts the automation', function () {
    $action = new UnsubscribeAction();

    test()->assertTrue($action->shouldHalt(Subscriber::factory()->create()));
});

it('wont continue', function () {
    $action = new UnsubscribeAction();

    test()->assertFalse($action->shouldContinue(Subscriber::factory()->create()));
});
