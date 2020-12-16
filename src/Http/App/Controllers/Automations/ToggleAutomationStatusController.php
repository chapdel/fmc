<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations;

use Spatie\Mailcoach\Enums\AutomationStatus;
use Spatie\Mailcoach\Models\Automation;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class ToggleAutomationStatusController
{
    use UsesMailcoachModels;

    public function __invoke(Automation $automation)
    {
        $automation->update([
            'status' => $automation->status === AutomationStatus::PAUSED
                ? AutomationStatus::STARTED
                : AutomationStatus::PAUSED,
        ]);

        flash()->success(__('Automation :automation was updated to :status.', ['automation' => $automation->name, 'status' => $automation->status]));

        return redirect()->route('mailcoach.automations');
    }
}
