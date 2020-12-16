<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Automation\Actions\AddActionToAutomationAction;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Support\Traits\UsesMailcoachModels;

class AutomationActionsController
{
    use UsesMailcoachModels;

    public function index(Automation $automation)
    {
        $actions = $automation->actions
            ->map(fn(Action $action) => [
                'class' => get_class($action->action),
                'data' => $action->action->toArray(),
            ])
            ->toArray();

        return view('mailcoach::app.automations.actions', compact(
            'automation',
            'actions',
        ));
    }

    public function store(
        Automation $automation,
        Request $request,
        AddActionToAutomationAction $addActionToAutomationAction
    ) {
        // TODO: Need a sync, no clean slate
        $automation->actions()->delete();

        $actions = json_decode($request->get('actions'), associative: true);

        foreach ($actions as $index => $action) {
            $actionClass = $action['class'];
            /** @var \Spatie\Mailcoach\Domain\Automation\Models\Concerns\AutomationAction $action */
            $action = $actionClass::make($action['data']);
            $action->store($automation, $index);
        }

        flash()->success(__('Actions successfully saved to automation :automation.', [
            'automation' => $automation->name,
        ]));

        return redirect()->route('mailcoach.automations.actions', $automation);
    }

    public function destroy(Automation $automation, Action $action)
    {
        $automation->actions->find($action)->delete();

        flash()->success(__('Action :action successfully deleted from automation :automation.', [
            'action' => $action->action->getName(),
            'automation' => $automation->name,
        ]));

        return redirect()->route('mailcoach.automations.actions', $automation);
    }
}
