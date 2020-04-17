<?php

namespace Spatie\Mailcoach\Support\Segments;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;

abstract class Segment
{
    protected CampaignConcern $campaign;

    public function setCampaign(CampaignConcern $campaign): self
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
