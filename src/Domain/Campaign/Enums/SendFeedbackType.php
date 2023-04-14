<?php

namespace Spatie\Mailcoach\Domain\Campaign\Enums;

enum SendFeedbackType: string
{
    case Bounce = 'bounce'; // @todo rename to HardBounce on new major version
    case SoftBounce = 'soft_bounce';
    case Complaint = 'complaint';
}
