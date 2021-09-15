<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

it('registers a macro on the date facade', function () {
    Date::setTestNow('2020-08-12 09:17');

    test()->assertEquals('2020-08-12 09:17', Date::now()->toMailcoachFormat());
    test()->assertEquals('2020-08-12 09:17', Carbon::now()->toMailcoachFormat());
});

it('transforms the date to the app timezone', function () {
    config()->set('app.timezone', 'Europe/Brussels');

    Date::setTestNow('2020-08-12 09:17');

    test()->assertEquals('2020-08-12 11:17', Date::now()->toMailcoachFormat());
    test()->assertEquals('2020-08-12 11:17', Carbon::now()->toMailcoachFormat());
});
