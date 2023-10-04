<?php

namespace Spatie\Mailcoach\Domain\Vendor\Sendinblue;

use Spatie\Mailcoach\Domain\Vendor\Sendinblue\Events\ClickEvent;
use Spatie\Mailcoach\Domain\Vendor\Sendinblue\Events\ComplaintEvent;
use Spatie\Mailcoach\Domain\Vendor\Sendinblue\Events\OpenEvent;
use Spatie\Mailcoach\Domain\Vendor\Sendinblue\Events\OtherEvent;
use Spatie\Mailcoach\Domain\Vendor\Sendinblue\Events\PermanentBounceEvent;
use Spatie\Mailcoach\Domain\Vendor\Sendinblue\Events\SendinblueEvent;
use Spatie\Mailcoach\Domain\Vendor\Sendinblue\Events\SoftBounceEvent;

class SendinblueEventFactory
{
    protected static array $sendinblueEvents = [
        ClickEvent::class,
        ComplaintEvent::class,
        OpenEvent::class,
        PermanentBounceEvent::class,
        SoftBounceEvent::class,
    ];

    public static function createForPayload(array $payload): SendinblueEvent
    {
        $sendinblueEvent = collect(static::$sendinblueEvents)
            ->map(fn (string $sendinblueEventClass) => new $sendinblueEventClass($payload))
            ->first(fn (SendinblueEvent $sendinblueEvent) => $sendinblueEvent->canHandlePayload());

        return $sendinblueEvent ?? new OtherEvent($payload);
    }
}
