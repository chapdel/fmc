<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Events;

use Spatie\Mailcoach\Domain\Content\Models\Open;

class TransactionalMailOpenedEvent
{
    public function __construct(
        public Open $transactionalMailOpen,
    ) {
    }
}
