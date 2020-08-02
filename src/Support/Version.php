<?php

namespace Spatie\Mailcoach\Support;

use Carbon\CarbonInterval;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PackageVersions\Versions;

class Version
{
    public static $versionEndpoint = 'https://mailcoach.app/api/latest-version';

    public function isLatest(): bool
    {
        $latestVersionInfo = $this->getLatestVersionInfo();

        if ($latestVersionInfo['version'] === 'unknown') {
            return true;
        }

        return $latestVersionInfo['version'] === $this->getCurrentVersion();
    }

    public function getFullVersion(): string
    {
        return Versions::getVersion('spatie/laravel-mailcoach');
    }

    public function getHashedFullVersion(): string
    {
        return md5($this->getFullVersion());
    }

    public function getCurrentVersion(): string
    {
        return Str::before($this->getFullVersion(), '@');
    }

    public function getLatestVersionInfo(): array
    {
        if (! Cache::has('mailcoach-latest-version-attempt-failed')) {
            try {
                $latestVersionInfo = Cache::remember('mailcoach-latest-version', (int)CarbonInterval::day()->totalSeconds, function () {
                    return Http::asJson()->get(static::$versionEndpoint)->json();
                });
            } catch (Exception $exception) {
                Cache::put('mailcoach-latest-version-attempt-failed', 1, (int)CarbonInterval::day()->totalSeconds);
            }
        }

        $defaults = [
            'version' => 'unknown',
            'released_at' => 'unknown',
        ];

        return array_merge($defaults, $latestVersionInfo ?? []);
    }
}
