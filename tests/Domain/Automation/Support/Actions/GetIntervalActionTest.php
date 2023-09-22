<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Support\Actions;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\GetIntervalAction;
use Spatie\TestTime\TestTime;

beforeEach(function () {
    $this->action = new GetIntervalAction();

    /** @var Carbon $fixedDate */
    $fixedDate = Carbon::make('2023-09-13');

    TestTime::freeze($fixedDate);
    expect(now()->englishDayOfWeek)->toBe('Wednesday');
});

it('can handle days', function () {
    $result = $this->action->execute(5, 'days');

    expect($result)->toBeInstanceOf(CarbonInterval::class);
    expect($result->totalSeconds)->toBe(432000);

    $upcomingDate = now()->add($result);

    expect($upcomingDate->toDateTimeString())->toEqual('2023-09-18 00:00:00');
    expect($upcomingDate->englishDayOfWeek)->toEqual('Monday');
});

it('can handle weekdays', function () {
    $result = $this->action->execute(5, 'weekdays');

    expect($result)->toBeInstanceOf(CarbonInterval::class);
    expect($result->totalSeconds)->toBe(604800);

    $upcomingDate = now()->add($result);

    expect($upcomingDate->toDateTimeString())->toEqual('2023-09-20 00:00:00');
    expect($upcomingDate->englishDayOfWeek)->toEqual('Wednesday');
});

it('can handle multiple weeks', function () {
    $result = $this->action->execute(35, 'weekdays');

    expect($result)->toBeInstanceOf(CarbonInterval::class);
    expect($result->totalSeconds)->toBe(3888000);

    $upcomingDate = now()->add($result);

    expect($upcomingDate->toDateTimeString())->toEqual('2023-10-28 00:00:00');
    expect($upcomingDate->englishDayOfWeek)->toEqual('Saturday');
});

it('can handle stopping in the middle of the weekend', function () {
    $result = $this->action->execute(3, 'weekdays');

    expect($result)->toBeInstanceOf(CarbonInterval::class);
    expect($result->totalSeconds)->toBe(432000);

    $upcomingDate = now()->add($result);

    expect($upcomingDate->toDateTimeString())->toEqual('2023-09-18 00:00:00');
    expect($upcomingDate->englishDayOfWeek)->toEqual('Monday');
});
