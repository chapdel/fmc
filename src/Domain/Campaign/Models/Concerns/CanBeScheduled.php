<?php

namespace Spatie\Mailcoach\Domain\Campaign\Models\Concerns;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;

trait CanBeScheduled
{
    public function scheduleToBeSentAt(CarbonInterface $carbon)
    {
        $this->update([
            'scheduled_at' => $carbon->utc(),
        ]);

        return $this;
    }

    public function markAsUnscheduled()
    {
        $this->update([
            'scheduled_at' => null,
        ]);

        return $this;
    }

    public function scopeScheduled(Builder $query): void
    {
        $query
            ->whereNotNull('scheduled_at')
            ->where('status', CampaignStatus::Draft);
    }

    public function scopeShouldBeSentNow(Builder $query)
    {
        $query
            ->scheduled()
            ->where('scheduled_at', '<=', now()->utc()->format('Y-m-d H:i:s'));
    }

    public function getScheduledAtAttribute($timestamp)
    {
        if (! $timestamp) {
            return null;
        }

        return Date::parse($timestamp, 'utc');
    }
}
