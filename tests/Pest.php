<?php

use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

expect()->extend('timePassedInSeconds', function ($expectedPassedInSeconds) {
    $actualPassedInSeconds = $this->value->diffInSeconds();

    expect($actualPassedInSeconds)->toBe($expectedPassedInSeconds);
});
