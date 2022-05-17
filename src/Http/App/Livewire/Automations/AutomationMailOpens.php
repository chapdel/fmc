<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Automations;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Queries\AutomationMailOpensQuery;

class AutomationMailOpens extends DataTable
{
    public string $sort = '-first_opened_at';

    public AutomationMail $mail;

    public function mount(AutomationMail $automationMail)
    {
        $this->mail = $automationMail;
    }

    public function getTitle(): string
    {
        return __('mailcoach - Opens');
    }

    public function getView(): string
    {
        return 'mailcoach::app.automations.mails.opens';
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.automations.mails.layouts.automationMail';
    }

    public function getLayoutData(): array
    {
        return [
            'mail' => $this->mail,
        ];
    }

    public function getData(Request $request): array
    {
        $automationMailOpens = (new AutomationMailOpensQuery($this->mail, $request));

        return [
            'mail' => $this->mail,
            'mailOpens' => $automationMailOpens->paginate(),
            'totalMailOpensCount' => $automationMailOpens->totalCount,
        ];
    }
}
