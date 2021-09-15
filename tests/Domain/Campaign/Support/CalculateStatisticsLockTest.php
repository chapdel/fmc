<?php

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Support\CalculateStatisticsLock;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

uses(TestCase::class);

beforeEach(function () {
    test()->campaign = Campaign::factory()->create();

    test()->lock = new CalculateStatisticsLock(test()->campaign);

    TestTime::freeze();
});

it('can lock and release', function () {
    test()->assertTrue(test()->lock->get());

    test()->assertFalse(test()->lock->get());

    test()->lock->release();

    test()->assertTrue(test()->lock->get());
});

it('will automatically expire the lock after 10 seconds', function () {
    TestTime::freeze()->addDay();
    
    test()->assertTrue(test()->lock->get());

    test()->assertFalse(test()->lock->get());

    TestTime::addSeconds(9);
    test()->assertFalse(test()->lock->get());

    TestTime::addSecond();
    test()->assertTrue(test()->lock->get());
    test()->assertFalse(test()->lock->get());

    TestTime::addSeconds(9);
    test()->assertFalse(test()->lock->get());

    TestTime::addSecond();
    test()->assertTrue(test()->lock->get());
});
