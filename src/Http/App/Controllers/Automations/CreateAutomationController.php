<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Actions\Automations\CreateAutomationAction;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class CreateAutomationController
{
    use UsesMailcoachModels;

    public function __invoke(Request $request, CreateAutomationAction $createAutomationAction)
    {
        $automation = $createAutomationAction->execute($request->validate(['name' => ['required']]));

        flash()->success(__('Automation :automation was created.', ['automation' => $automation->name]));

        return redirect()->route('mailcoach.automations.settings', $automation);
    }
}
