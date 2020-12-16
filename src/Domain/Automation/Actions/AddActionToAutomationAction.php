<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\Concerns\AutomationAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class AddActionToAutomationAction
{
    use UsesMailcoachModels;

    public function execute(Automation $automation, AutomationAction $action): Automation
    {
        $automation->addAction($action);

        return $automation->fresh('actions');
    }
}
