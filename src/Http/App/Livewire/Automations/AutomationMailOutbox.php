<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Automations;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Queries\AutomationMailOpensQuery;
use Spatie\Mailcoach\Http\App\Queries\AutomationMailSendsQuery;

class AutomationMailOutbox extends DataTable
{
    public string $sort = '-sent_at';

    public AutomationMail $mail;

    public function mount(AutomationMail $automationMail)
    {
        $this->mail = $automationMail;
    }

    public function getTitle(): string
    {
        return __('mailcoach - Outbox');
    }

    public function getView(): string
    {
        return 'mailcoach::app.automations.mails.outbox';
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

    public function getData(): array
    {
        $this->authorize('view', $this->mail);

        $sendsQuery = (new AutomationMailSendsQuery($this->mail, request()));

        return [
            'mail' => $this->mail,
            'sends' => $sendsQuery->paginate(),
            'totalSends' => $this->mail->sends()->count(),
            'totalPending' => $this->mail->sends()->pending()->count(),
            'totalSent' => $this->mail->sends()->sent()->count(),
            'totalFailed' => $this->mail->sends()->failed()->count(),
            'totalBounces' => $this->mail->sends()->bounced()->count(),
            'totalComplaints' => $this->mail->sends()->complained()->count(),
        ];
    }
}
