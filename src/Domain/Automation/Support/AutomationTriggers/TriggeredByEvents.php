<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\AutomationTriggers;

interface TriggeredByEvents
{
    public function subscribe($events): void;
}
