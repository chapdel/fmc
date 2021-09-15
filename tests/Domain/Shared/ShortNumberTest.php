<?php

use Illuminate\Support\Str;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

it('can shorten a number', function () {
    test()->assertEquals('1', Str::shortNumber(1));
    test()->assertEquals('100', Str::shortNumber(100));
    test()->assertEquals('500', Str::shortNumber(500));
    test()->assertEquals('999', Str::shortNumber(999));
    test()->assertEquals('1K', Str::shortNumber(1000));
    test()->assertEquals('1.7K', Str::shortNumber(1799));

    test()->assertEquals('999.9K', Str::shortNumber(999_999));
    test()->assertEquals('1M', Str::shortNumber(1_000_000));

    test()->assertEquals('🤯', Str::shortNumber(1_000_000_000));
});
