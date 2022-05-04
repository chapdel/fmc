<?php

namespace Spatie\Mailcoach\Http\App\Livewire;

use Livewire\WithPagination;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\AutomationsQuery;
use Spatie\Mailcoach\Http\App\Queries\TemplatesQuery;

class AutomationIndex extends DataTable
{
    public function deleteAutomation(int $id)
    {
        $automation = self::getAutomationClass()::find($id);

        $this->authorize('delete', $automation);

        $automation->delete();

        $this->dispatchBrowserEvent('notify', [
            'content' => __('mailcoach - Automation :automation was deleted.', ['automation' => $automation->name]),
        ]);
    }

    public function render()
    {
        parent::render();

        return view('mailcoach::app.automations.index', [
            'automations' => (new AutomationsQuery(request()))->paginate(),
            'totalAutomationsCount' => self::getAutomationClass()::count(),
        ])->layout('mailcoach::app.layouts.main', [
            'title' => __('mailcoach - Templates'),
        ]);
    }
}
