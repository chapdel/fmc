<?php

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\HaltAction;

it('halts the automation', function () {
    $action = new HaltAction();

    expect($action->shouldHalt(ActionSubscriber::create([
        'subscriber_id' => Subscriber::factory()->create()->id,
        'action_id' => Action::factory()->create()->id,
    ])))->toBeTrue();
});

it('wont continue', function () {
    $action = new HaltAction();

    expect($action->shouldContinue(ActionSubscriber::create([
        'subscriber_id' => Subscriber::factory()->create()->id,
        'action_id' => Action::factory()->create()->id,
    ])))->toBeFalse();
});
