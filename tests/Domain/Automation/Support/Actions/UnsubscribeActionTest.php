<?php

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\UnsubscribeAction;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

it('unsubscribes the subscriber', function () {
    $action = new UnsubscribeAction();

    /** @var Subscriber $subscriber */
    $subscriber = Subscriber::factory()->create();

    expect($subscriber->isSubscribed())->toBeTrue();

    $action->run($subscriber);

    expect($subscriber->fresh()->isSubscribed())->toBeFalse();
});

it('halts the automation', function () {
    $action = new UnsubscribeAction();

    expect($action->shouldHalt(Subscriber::factory()->create()))->toBeTrue();
});

it('wont continue', function () {
    $action = new UnsubscribeAction();

    expect($action->shouldContinue(Subscriber::factory()->create()))->toBeFalse();
});
