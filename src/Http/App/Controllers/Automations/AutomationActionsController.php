<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class AutomationActionsController
{
    use UsesMailcoachModels;

    public function index(Automation $automation)
    {
        $actions = $automation->actions()
            ->withCount(['completedSubscribers', 'activeSubscribers'])
            ->get()
            ->map(fn(Action $action) => [
                'uuid' => $action->uuid,
                'class' => get_class($action->action),
                'data' => $action->action->toArray(),
                'active' => $action->active_subscribers_count,
                'completed' => $action->completed_subscribers_count,
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
    ) {
        $newActions = json_decode($request->get('actions'), associative: true);

        $automation->chain($newActions);

        flash()->success(__('Actions successfully saved to automation :automation.', [
            'automation' => $automation->name,
        ]));

        return redirect()->route('mailcoach.automations.actions', $automation);
    }
}
