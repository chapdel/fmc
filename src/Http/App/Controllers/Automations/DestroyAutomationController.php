<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations;

use Spatie\Mailcoach\Models\Automation;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class DestroyAutomationController
{
    use UsesMailcoachModels;

    public function __invoke(Automation $automation)
    {
        $automation->delete();

        flash()->success(__('Automation :automation was deleted.', ['automation' => $automation->name]));

        return redirect()->route('mailcoach.automations');
    }
}
