<?php

use Illuminate\Support\Str;
use Spatie\Mailcoach\Tests\TestCase;



it('can shorten a number', function () {
    expect(Str::shortNumber(1))->toEqual('1');
    expect(Str::shortNumber(100))->toEqual('100');
    expect(Str::shortNumber(500))->toEqual('500');
    expect(Str::shortNumber(999))->toEqual('999');
    expect(Str::shortNumber(1000))->toEqual('1K');
    expect(Str::shortNumber(1799))->toEqual('1.7K');

    test()->assertEquals('999.9K', Str::shortNumber(999_999));
    test()->assertEquals('1M', Str::shortNumber(1_000_000));

    test()->assertEquals('🤯', Str::shortNumber(1_000_000_000));
});
