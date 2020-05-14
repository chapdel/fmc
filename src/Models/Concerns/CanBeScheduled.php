<?php

namespace Spatie\Mailcoach\Models\Concerns;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Enums\CampaignStatus;

trait CanBeScheduled
{
    public function scheduleToBeSentAt(CarbonInterface $carbon)
    {
        $this->update([
            'scheduled_at' => $carbon,
            'last_modified_at' => now(),
        ]);

        return $this;
    }

    public function markAsUnscheduled()
    {
        $this->update([
            'scheduled_at' => null,
            'last_modified_at' => now(),
        ]);

        return $this;
    }

    public function scopeScheduled(Builder $query): void
    {
        $query
            ->whereNotNull('scheduled_at')
            ->where('status', CampaignStatus::DRAFT);
    }

    public function scopeShouldBeSentNow(Builder $query)
    {
        $query
            ->scheduled()
            ->where('scheduled_at', '<=', now()->format('Y-m-d H:i:s'));
    }
}
