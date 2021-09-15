<?php

use Illuminate\Support\Facades\Cache;
use Spatie\Mailcoach\Domain\Shared\Support\Version;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    test()->version = app(Version::class);
});

it('can get the current version', function () {
    expect(test()->version->getCurrentVersion())->toBeString();
});

it('can get the latest version', function () {
    Cache::clear();

    $latestVersion = test()->version->getLatestVersionInfo();

    test()->assertArrayHasKey('version', $latestVersion);
    test()->assertArrayHasKey('released_at', $latestVersion);

    test()->assertNotEquals('unknown', $latestVersion['version']);
});
