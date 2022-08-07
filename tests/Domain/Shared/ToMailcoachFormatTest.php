<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;

it('registers a macro on the date facade', function () {
    Date::setTestNow('2020-08-12 09:17');

    expect(Date::now()->toMailcoachFormat())->toEqual('2020-08-12 09:17');
    expect(Carbon::now()->toMailcoachFormat())->toEqual('2020-08-12 09:17');
});

it('transforms the date to the app timezone', function () {
    config()->set('app.timezone', 'Europe/Brussels');

    Date::setTestNow('2020-08-12 09:17');

    expect(Date::now()->toMailcoachFormat())->toEqual('2020-08-12 11:17');
    expect(Carbon::now()->toMailcoachFormat())->toEqual('2020-08-12 11:17');
});

it('transforms the date to the mailcoach timezone', function () {
    config()->set('app.timezone', 'UTC');
    config()->set('mailcoach.timezone', 'Europe/Brussels');

    Date::setTestNow('2020-08-12 09:17');

    expect(Date::now()->toMailcoachFormat())->toEqual('2020-08-12 11:17');
    expect(Carbon::now()->toMailcoachFormat())->toEqual('2020-08-12 11:17');
});
