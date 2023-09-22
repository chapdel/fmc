<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Events;

use Spatie\Mailcoach\Domain\Content\Models\Click;

class TransactionalMailLinkClickedEvent
{
    public function __construct(
        public Click $click,
    ) {
    }
}
