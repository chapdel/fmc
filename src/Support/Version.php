<?php

namespace Spatie\Mailcoach\Support;

use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Cache;
use PackageVersions\Versions;

class Version
{
    public static $versionEndpoint = 'https://mailcoach.app/api/latest-version';

    protected HttpClient $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function isLatest(): bool
    {
        $latestVersionInfo = $this->getLatestVersionInfo();

        if ($latestVersionInfo['version'] === 'unknown') {
            return true;
        }

        return $latestVersionInfo['version'] === $this->getCurrentVersion();
    }

    public function getCurrentVersion(): string
    {
        return Versions::getVersion('spatie/laravel-mailcoach');
    }

    public function getLatestVersionInfo(): array
    {
        $latestVersionInfo = Cache::remember('mailcoach-latest-version', CarbonInterval::day()->seconds, function () {
            return $this->httpClient->getJson(static::$versionEndpoint);
        });

        $defaults = [
            'version' => 'unknown',
            'released_at' => 'unknown',
        ];

        return array_merge($defaults, $latestVersionInfo);
    }
}
