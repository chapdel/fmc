<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations;

use Spatie\Mailcoach\Models\Action;
use Spatie\Mailcoach\Models\Automation;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class DuplicateAutomationController
{
    use UsesMailcoachModels;

    public function __invoke(Automation $automation)
    {
        /** @var \Spatie\Mailcoach\Models\Automation $duplicateAutomation */
        $duplicateAutomation = $this->getAutomationClass()::create([
            'name' => __('Duplicate of') . ' ' . $automation->name,
        ]);

        $automation->actions->each(function (Action $action) use ($duplicateAutomation) {
            $duplicateAutomation->actions()->save(Action::make([
                'action' => $action->action,
            ]));
        });

        flash()->success(__('Automation :automation was duplicated.', ['automation' => $automation->name]));

        return redirect()->route('mailcoach.automations.settings', $duplicateAutomation);
    }
}
