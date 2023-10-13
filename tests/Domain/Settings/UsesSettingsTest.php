<?php

use Spatie\Mailcoach\Domain\Settings\Models\Setting;
use Spatie\Mailcoach\Domain\Settings\Support\AppConfiguration\AppConfiguration;

it('caches the settings even if there are none', function () {

    $appConfiguration = app(AppConfiguration::class);
    $appConfiguration->empty();

    $count = 0;
    \Illuminate\Support\Facades\DB::listen(function ($query) use (&$count) {
        $count++;
    });

    expect(Setting::count())->toBe(0);

    $before = $count;

    $appConfiguration->getSettings();
    $appConfiguration->getSettings();

    expect($count)->toBe($before + 1);
});
