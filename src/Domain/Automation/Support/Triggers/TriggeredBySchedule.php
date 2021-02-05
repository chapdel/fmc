<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Triggers;

use Spatie\Mailcoach\Domain\Automation\Models\Automation;

interface TriggeredBySchedule
{
    public function trigger(Automation $automation): void;
}
