<?php

namespace Spatie\Mailcoach\Support;

use Illuminate\Support\Facades\Cache;
use Spatie\Mailcoach\Models\Campaign;

class CalculateStatisticsLock
{
    private string $key;

    private int $lockTimeInSeconds;

    public function __construct(Campaign $campaign, int $lockTimeInSeconds = 10)
    {
        $this->key = "calculate-statistics-lock-{$campaign->id}";

        $this->lockTimeInSeconds = $lockTimeInSeconds;
    }

    public function get(): bool
    {
        $cachedValue = Cache::get($this->key, null);

        if (is_null($cachedValue)) {
            $this->setLock();

            return true;
        }

        if (now()->timestamp >= $cachedValue) {
            $this->setLock();

            return true;
        }

        return false;
    }

    public function release(): void
    {
        Cache::set($this->key, 0);
    }

    protected function setLock()
    {
        Cache::set($this->key, now()->addSeconds($this->lockTimeInSeconds)->timestamp);
    }
}
