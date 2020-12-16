<?php

namespace Spatie\Mailcoach\Domain\Campaign\Enums;

class SubscriptionStatus
{
    public const UNCONFIRMED = 'unconfirmed';
    public const SUBSCRIBED = 'subscribed';
    public const UNSUBSCRIBED = 'unsubscribed';
}
