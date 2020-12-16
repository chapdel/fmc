<?php

namespace Spatie\Mailcoach\Events;

use Spatie\Mailcoach\Models\TransactionalMailClick;

class TransactionalMailLinkClickedEvent
{
    public function __construct(
        public TransactionalMailClick $campaignClick,
    ) {}
}
