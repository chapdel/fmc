<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Automations;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Queries\AutomationMailLinksQuery;
use Spatie\Mailcoach\MainNavigation;

class AutomationMailClicks extends DataTable
{
    public string $sort = '-unique_click_count';

    public AutomationMail $mail;

    public function mount(AutomationMail $automationMail)
    {
        $this->mail = $automationMail;

        app(MainNavigation::class)->activeSection()?->add($this->mail->name, route('mailcoach.automations.mails'));
    }

    public function getTitle(): string
    {
        return __('mailcoach - Clicks');
    }

    public function getView(): string
    {
        return 'mailcoach::app.automations.mails.clicks';
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
        return [
            'mail' => $this->mail,
            'links' => (new AutomationMailLinksQuery($this->mail, $request))->paginate(),
            'totalLinksCount' => $this->mail->links()->count(),
        ];
    }
}
