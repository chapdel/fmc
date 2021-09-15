<?php

use Carbon\CarbonInterval;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
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

    test()->assertFalse($action->shouldHalt(test()->subscriber));

    TestTime::addDay();

    test()->assertFalse($action->shouldHalt(test()->subscriber));
});

it('will only continue when time has passed', function () {
    $action = new WaitAction(CarbonInterval::days(1));

    test()->assertFalse($action->shouldContinue(test()->subscriber));

    TestTime::addDay();

    test()->assertTrue($action->shouldContinue(test()->subscriber));
});
