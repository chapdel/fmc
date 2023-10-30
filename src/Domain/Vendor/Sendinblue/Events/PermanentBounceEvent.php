<?php

namespace Spatie\Mailcoach\Domain\Vendor\Sendinblue\Events;

use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Vendor\Sendinblue\Enums\BounceType;

class PermanentBounceEvent extends SendinblueEvent
{
    public function canHandlePayload(): bool
    {
        return $this->event === BounceType::Hard->value;
    }

    public function handle(Send $send): void
    {
        $send->registerBounce($this->getTimestamp());
    }
}
