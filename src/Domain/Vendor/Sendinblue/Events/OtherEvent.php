<?php

namespace Spatie\Mailcoach\Domain\Vendor\Sendinblue\Events;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class OtherEvent extends SendinblueEvent
{
    public function canHandlePayload(): bool
    {
        return true;
    }

    public function handle(Send $send): void
    {
    }
}
