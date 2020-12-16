<?php

namespace Spatie\Mailcoach\Events;

use Spatie\Mailcoach\Models\TransactionalMailOpen;

class TransactionalMailOpenedEvent
{
    public function __construct(
        public TransactionalMailOpen $transactionalMailOpen,
    ) {}
}
