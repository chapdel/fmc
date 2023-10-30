<?php

namespace Spatie\Mailcoach\Domain\Vendor\Sendinblue\Enums;

/** reference: https://developers.sendinblue.com/docs/transactional-webhooks */
enum BounceType: string
{
    case Soft = 'soft_bounce';
    case Hard = 'hard_bounce';
}
