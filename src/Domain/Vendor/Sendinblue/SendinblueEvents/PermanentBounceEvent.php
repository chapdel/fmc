<?php

namespace Spatie\MailcoachSendinblueFeedback\SendinblueEvents;

use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\MailcoachSendinblueFeedback\Enums\BounceType;

class PermanentBounceEvent extends SendinblueEvent
{
    public function canHandlePayload(): bool
    {
        return $this->event === BounceType::Hard->value;
    }

    public function handle(Send $send)
    {
        $send->registerBounce($this->getTimestamp());
    }
}
