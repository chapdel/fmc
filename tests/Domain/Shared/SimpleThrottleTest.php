<?php

use Spatie\Mailcoach\Domain\Shared\Support\Throttling\SimpleThrottle;
use Spatie\TestTime\TestTime;

beforeEach(function () {
    $this->simpleThrottle = app(SimpleThrottle::class);

    TestTime::unfreeze();

    $this->startedAt = now();
});

it('can throttle hits', function () {
    $this->simpleThrottle
        ->inSeconds(3)
        ->allow(2);

    $this->simpleThrottle->hit();
    expect($this->startedAt)->timePassedInSeconds(0);

    $this->simpleThrottle->hit();
    expect($this->startedAt)->timePassedInSeconds(0);

    $this->simpleThrottle->hit();
    expect($this->startedAt)->timePassedInSeconds(3);

    $this->simpleThrottle->hit();
    expect($this->startedAt)->timePassedInSeconds(3);

    $this->simpleThrottle->hit();
    expect($this->startedAt)->timePassedInSeconds(6);
});
