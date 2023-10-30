<?php

namespace Spatie\Mailcoach\Domain\Vendor\Sendinblue\Events;

use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Vendor\Sendinblue\Enums\BounceType;

class SoftBounceEvent extends SendinblueEvent
{
    public function canHandlePayload(): bool
    {
        return $this->event === BounceType::Soft->value;
    }

    public function handle(Send $send): void
    {
        $send->registerBounce($this->getTimestamp(), softBounce: true);
    }
}
