<?php

namespace Spatie\Mailcoach\Http\App\Livewire;

use Spatie\Mailcoach\Http\App\Queries\AutomatedMailQuery;

class AutomationMailIndex extends DataTable
{
    public function deleteAutomationMail(int $id)
    {
        $automationMail = self::getAutomationMailClass()::find($id);

        $this->authorize('delete', $automationMail);

        $automationMail->delete();

        $this->dispatchBrowserEvent('notify', [
            'content' => __('mailcoach - Automation Email :automationMail was deleted.', ['automationMail' => $automationMail->name]),
        ]);
    }

    public function render()
    {
        parent::render();

        return view('mailcoach::app.automations.mails.index', [
            'automationMails' => (new AutomatedMailQuery(request()))->paginate(),
            'totalAutomationMailsCount' => self::getAutomationMailClass()::count(),
        ])->layout('mailcoach::app.layouts.main', [
            'title' => __('mailcoach - Automation Emails'),
        ]);
    }
}
