<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Automations;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Queries\AutomationMailUnsubscribesQuery;

class AutomationMailUnsubscribes extends DataTable
{
    public string $sort = '-created_at';

    public AutomationMail $mail;

    public function mount(AutomationMail $automationMail)
    {
        $this->mail = $automationMail;
    }

    public function getTitle(): string
    {
        return __('mailcoach - Unsubscribes');
    }

    public function getView(): string
    {
        return 'mailcoach::app.automations.mails.unsubscribes';
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
        $this->authorize('view', $this->mail);

        return [
            'mail' => $this->mail,
            'unsubscribes' => (new AutomationMailUnsubscribesQuery($this->mail, $request))->paginate(),
            'totalUnsubscribes' => $this->mail->unsubscribes()->count(),
        ];
    }
}
