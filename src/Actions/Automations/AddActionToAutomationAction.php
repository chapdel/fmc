<?php

namespace Spatie\Mailcoach\Actions\Automations;

use Spatie\Mailcoach\Models\Automation;
use Spatie\Mailcoach\Models\Concerns\AutomationAction;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class AddActionToAutomationAction
{
    use UsesMailcoachModels;

    public function execute(Automation $automation, AutomationAction $action): Automation
    {
        $automation->addAction($action);

        return $automation->fresh('actions');
    }
}
