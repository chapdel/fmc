<?php

namespace Spatie\Mailcoach\Domain\Automation\Events;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class AutomationMailStatisticsCalculatedEvent
{
    public function __construct(
        public AutomationMail $automationMail
    ) {
    }
}
