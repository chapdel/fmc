<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;

/**
 * @deprecated
 */
class PrepareSubjectAction
{
    public function execute(AutomationMail $automationMail): void
    {
        // Deprecated, by default we won't do anything here anymore
    }
}
