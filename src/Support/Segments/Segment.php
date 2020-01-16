<?php

namespace Spatie\Mailcoach\Support\Segments;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\Subscriber;

abstract class Segment
{
    protected Campaign $campaign;

    public function setCampaign(Campaign $campaign): self
    {
        $this->campaign = $campaign;

        return $this;
    }

    abstract public function description(): string;

    public function subscribersQuery(Builder $subscribersQuery): void
    {
    }

    public function shouldSend(Subscriber $subscriber): bool
    {
        return true;
    }
}
