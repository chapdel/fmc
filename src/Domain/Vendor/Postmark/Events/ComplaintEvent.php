<?php

namespace Spatie\Mailcoach\Domain\Vendor\Postmark\Events;

use Carbon\Carbon;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class ComplaintEvent extends PostmarkEvent
{
    public function canHandlePayload(): bool
    {
        if ($this->event === 'SpamComplaint') {
            return true;
        }

        if ($this->event === 'SubscriptionChange' && ($this->payload['SuppressionReason'] ?? null) === 'SpamComplaint') {
            return true;
        }

        return false;
    }

    public function handle(Send $send): void
    {
        $complainedAt = Carbon::parse($this->payload['BouncedAt'] ?? $this->payload['ChangedAt']);

        $send->registerComplaint($complainedAt);
    }
}
