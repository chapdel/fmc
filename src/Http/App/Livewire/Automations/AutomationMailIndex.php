<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Automations;

use Spatie\Mailcoach\Http\App\Livewire\DataTable;
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

    public function getTitle(): string
    {
        return __('mailcoach - Automation Emails');
    }

    public function getView(): string
    {
        return 'mailcoach::app.automations.mails.index';
    }

    public function getData(): array
    {
        return [
            'automationMails' => (new AutomatedMailQuery(request()))->paginate(),
            'totalAutomationMailsCount' => self::getAutomationMailClass()::count(),
        ];
    }
}
