<?php

namespace Spatie\Mailcoach\Tests\Domain\Shared;

use Illuminate\Support\Facades\Cache;
use Spatie\Mailcoach\Domain\Shared\Support\Version;
use Spatie\Mailcoach\Tests\TestCase;

class VersionTest extends TestCase
{
    protected Version $version;

    public function setUp(): void
    {
        parent::setUp();

        $this->version = app(Version::class);
    }

    /** @test */
    public function it_can_get_the_current_version()
    {
        $this->assertIsString($this->version->getCurrentVersion());
    }

    /** @test */
    public function it_can_get_the_latest_version()
    {
        Cache::clear();

        $latestVersion = $this->version->getLatestVersionInfo();

        $this->assertArrayHasKey('version', $latestVersion);
        $this->assertArrayHasKey('released_at', $latestVersion);

        $this->assertNotEquals('unknown', $latestVersion['version']);
    }
}
