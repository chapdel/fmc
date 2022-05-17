<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Automations;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Queries\AutomatedMailQuery;

class AutomationMails extends DataTable
{
    public function duplicateAutomationMail(int $id)
    {
        $automationMail = self::getAutomationMailClass()::find($id);

        $this->authorize('create', $automationMail);

        /** @var AutomationMail $automationMail */
        $automationMail = self::getAutomationMailClass()::create([
            'name' => __('mailcoach - Duplicate of') . ' ' . $automationMail->name,
            'subject' => $automationMail->subject,
            'html' => $automationMail->html,
            'structured_html' => $automationMail->structured_html,
            'webview_html' => $automationMail->webview_html,
            'track_opens' => $automationMail->track_opens,
            'track_clicks' => $automationMail->track_clicks,
            'utm_tags' => $automationMail->utm_tags,
            'last_modified_at' => now(),
        ]);

        flash()->success(__('mailcoach - Email :name was duplicated.', ['name' => $automationMail->name]));

        return redirect()->route('mailcoach.automations.mails.settings', $automationMail);
    }

    public function deleteAutomationMail(int $id)
    {
        $automationMail = self::getAutomationMailClass()::find($id);

        $this->authorize('delete', $automationMail);

        $automationMail->delete();

        $this->flash(__('mailcoach - Automation Email :automationMail was deleted.', ['automationMail' => $automationMail->name]));
    }

    public function getTitle(): string
    {
        return __('mailcoach - Automation Emails');
    }

    public function getView(): string
    {
        return 'mailcoach::app.automations.mails.index';
    }

    public function getData(Request $request): array
    {
        return [
            'automationMails' => (new AutomatedMailQuery($request))->paginate(),
            'totalAutomationMailsCount' => self::getAutomationMailClass()::count(),
        ];
    }
}
