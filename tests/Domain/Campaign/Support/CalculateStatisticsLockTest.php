<?php

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Support\CalculateStatisticsLock;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;



beforeEach(function () {
    test()->campaign = Campaign::factory()->create();

    test()->lock = new CalculateStatisticsLock(test()->campaign);

    TestTime::freeze();
});

it('can lock and release', function () {
    expect(test()->lock->get())->toBeTrue();

    expect(test()->lock->get())->toBeFalse();

    test()->lock->release();

    expect(test()->lock->get())->toBeTrue();
});

it('will automatically expire the lock after 10 seconds', function () {
    TestTime::freeze()->addDay();

    expect(test()->lock->get())->toBeTrue();

    expect(test()->lock->get())->toBeFalse();

    TestTime::addSeconds(9);
    expect(test()->lock->get())->toBeFalse();

    TestTime::addSecond();
    expect(test()->lock->get())->toBeTrue();
    expect(test()->lock->get())->toBeFalse();

    TestTime::addSeconds(9);
    expect(test()->lock->get())->toBeFalse();

    TestTime::addSecond();
    expect(test()->lock->get())->toBeTrue();
});
