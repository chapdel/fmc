<?php

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\UnsubscribeAction;

it('unsubscribes the subscriber', function () {
    $action = new UnsubscribeAction();

    /** @var Subscriber $subscriber */
    $subscriber = Subscriber::factory()->create();

    expect($subscriber->isSubscribed())->toBeTrue();

    $action->run(ActionSubscriber::factory()->create([
        'subscriber_id' => $subscriber->id,
    ]));

    expect($subscriber->fresh()->isSubscribed())->toBeFalse();
});

it('halts the automation', function () {
    $action = new UnsubscribeAction();

    expect($action->shouldHalt(ActionSubscriber::factory()->create()))->toBeTrue();
});

it('wont continue', function () {
    $action = new UnsubscribeAction();

    expect($action->shouldContinue(ActionSubscriber::factory()->create()))->toBeFalse();
});
