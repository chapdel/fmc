<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Automations;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Automation\Actions\DuplicateAutomationAction;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;
use Spatie\Mailcoach\Http\App\Queries\AutomationsQuery;
use Spatie\Mailcoach\Mailcoach;

class AutomationsComponent extends DataTableComponent
{
    public function toggleAutomationStatus(int $id)
    {
        $automation = self::getAutomationClass()::findOrFail($id);

        $automation->update([
            'status' => $automation->status === AutomationStatus::Paused
                ? AutomationStatus::Started
                : AutomationStatus::Paused,
        ]);

        $this->dispatchBrowserEvent('notify', [
            'content' => __mc('Automation :automation was :status.', ['automation' => $automation->name, 'status' => $automation->status->value]),
        ]);
    }

    public function duplicateAutomation(int $id)
    {
        $automation = self::getAutomationClass()::find($id);

        /** @var DuplicateAutomationAction $action */
        $action = Mailcoach::getAutomationActionClass('duplicate_automation', DuplicateAutomationAction::class);
        $duplicateAutomation = $action->execute($automation);

        flash()->success(__mc('Automation :automation was duplicated.', ['automation' => $automation->name]));

        return redirect()->route('mailcoach.automations.settings', $duplicateAutomation);
    }

    public function deleteAutomation(int $id)
    {
        $automation = self::getAutomationClass()::find($id);

        $this->authorize('delete', $automation);

        $automation->delete();

        $this->flash(__mc('Automation :automation was deleted.', ['automation' => $automation->name]));
    }

    public function getTitle(): string
    {
        return __mc('Automations');
    }

    public function getView(): string
    {
        return 'mailcoach::app.automations.index';
    }

    public function getData(Request $request): array
    {
        return [
            'automations' => (new AutomationsQuery($request))->paginate($request->per_page),
            'totalAutomationsCount' => self::getAutomationClass()::count(),
        ];
    }
}
