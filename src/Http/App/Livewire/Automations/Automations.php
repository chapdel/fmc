<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Automations;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Queries\AutomationsQuery;

class Automations extends DataTable
{
    public function toggleAutomationStatus(int $id)
    {
        $automation = self::getAutomationClass()::findOrFail($id);

        $automation->update([
            'status' => $automation->status === AutomationStatus::PAUSED
                ? AutomationStatus::STARTED
                : AutomationStatus::PAUSED,
        ]);

        $this->dispatchBrowserEvent('notify', [
            'content' => __('mailcoach - Automation :automation was :status.', ['automation' => $automation->name, 'status' => $automation->status]),
        ]);
    }

    public function duplicateAutomation(int $id)
    {
        $automation = self::getAutomationClass()::find($id);

        /** @var \Spatie\Mailcoach\Domain\Automation\Models\Automation $duplicateAutomation */
        $duplicateAutomation = self::getAutomationClass()::create([
            'name' => __('mailcoach - Duplicate of') . ' ' . $automation->name,
        ]);

        $automation->actions->each(function (Action $action) use ($duplicateAutomation) {
            $actionClass = static::getAutomationActionModelClass();
            $newAction = $duplicateAutomation->actions()->save($actionClass::make([
                'action' => $action->action->duplicate(),
                'key' => $action->key,
                'order' => $action->order,
            ]));

            foreach ($action->children as $child) {
                $duplicateAutomation->actions()->save($actionClass::make([
                    'parent_id' => $newAction->id,
                    'action' => $child->action->duplicate(),
                    'key' => $child->key,
                    'order' => $child->order,
                ]));
            }
        });

        flash()->success(__('mailcoach - Automation :automation was duplicated.', ['automation' => $automation->name]));

        return redirect()->route('mailcoach.automations.settings', $duplicateAutomation);
    }

    public function deleteAutomation(int $id)
    {
        $automation = self::getAutomationClass()::find($id);

        $this->authorize('delete', $automation);

        $automation->delete();

        $this->flash(__('mailcoach - Automation :automation was deleted.', ['automation' => $automation->name]));
    }

    public function getTitle(): string
    {
        return __('mailcoach - Automations');
    }

    public function getView(): string
    {
        return 'mailcoach::app.automations.index';
    }

    public function getData(Request $request): array
    {
        return [
            'automations' => (new AutomationsQuery($request))->paginate(),
            'totalAutomationsCount' => self::getAutomationClass()::count(),
        ];
    }
}
