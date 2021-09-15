<?php

use Carbon\CarbonInterval;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\WaitAction;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

uses(TestCase::class);

beforeEach(function () {
    test()->action = Action::factory()->create();
    test()->action->subscribers()->attach(SubscriberFactory::new()->create());

    test()->subscriber = test()->action->subscribers->first();

    TestTime::freeze();
});

it('never halts the automation', function () {
    $action = new WaitAction(CarbonInterval::days(1));

    expect($action->shouldHalt(test()->subscriber))->toBeFalse();

    TestTime::addDay();

    expect($action->shouldHalt(test()->subscriber))->toBeFalse();
});

it('will only continue when time has passed', function () {
    $action = new WaitAction(CarbonInterval::days(1));

    expect($action->shouldContinue(test()->subscriber))->toBeFalse();

    TestTime::addDay();

    expect($action->shouldContinue(test()->subscriber))->toBeTrue();
});
