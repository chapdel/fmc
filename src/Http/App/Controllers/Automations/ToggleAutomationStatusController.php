<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations;

use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Support\Traits\UsesMailcoachModels;

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
