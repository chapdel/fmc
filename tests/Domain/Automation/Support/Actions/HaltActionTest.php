<?php

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\HaltAction;
use Spatie\Mailcoach\Tests\TestCase;



it('halts the automation', function () {
    $action = new HaltAction();

    expect($action->shouldHalt(Subscriber::factory()->create()))->toBeTrue();
});

it('wont continue', function () {
    $action = new HaltAction();

    expect($action->shouldContinue(Subscriber::factory()->create()))->toBeFalse();
});
