<?php

namespace Spatie\Mailcoach\Domain\Vendor\Mailgun;

enum EventType: string
{
    case Clicked = 'clicked';
    case Complained = 'complained';
    case Opened = 'opened';
    case PermanentFail = 'permanent_fail';
}
