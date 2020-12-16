<?php

namespace Spatie\Mailcoach\Enums;

class CampaignStatus
{
    const DRAFT = 'draft';
    const SENDING = 'sending';
    const SENT = 'sent';
    const CANCELLED = 'cancelled';
    const AUTOMATED = 'automated';
}
