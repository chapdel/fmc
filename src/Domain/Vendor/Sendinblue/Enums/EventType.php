<?php

namespace Spatie\Mailcoach\Domain\Vendor\Sendinblue\Enums;

enum EventType: string
{
    case Open = 'opened';
    case Click = 'click';
    case Bounce = 'hardBounce';
    case Spam = 'spam';
}
