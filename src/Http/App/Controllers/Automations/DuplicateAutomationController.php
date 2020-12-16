<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations;

use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Support\Traits\UsesMailcoachModels;

class DuplicateAutomationController
{
    use UsesMailcoachModels;

    public function __invoke(Automation $automation)
    {
        /** @var \Spatie\Mailcoach\Domain\Automation\Models\Automation $duplicateAutomation */
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
