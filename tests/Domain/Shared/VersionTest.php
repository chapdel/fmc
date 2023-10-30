<?php

use Illuminate\Support\Facades\Cache;
use Spatie\Mailcoach\Domain\Shared\Support\Version;

beforeEach(function () {
    test()->version = app(Version::class);
});

it('can get the current version', function () {
    expect(test()->version->getCurrentVersion())->toBeString();
});

it('can get the latest version', function () {
    Cache::clear();

    $latestVersion = test()->version->getLatestVersionInfo();

    expect($latestVersion)->toHaveKey('version');
    expect($latestVersion)->toHaveKey('released_at');

    test()->assertNotEquals('unknown', $latestVersion['version']);
});
