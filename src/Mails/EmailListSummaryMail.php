<?php

namespace Spatie\Mailcoach\Mails;

use Carbon\Carbon;
use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Models\EmailList;

class EmailListSummaryMail extends Mailable
{
    public string $theme = 'mailcoach::mails.layout.mailcoach';

    /** @var \Spatie\Mailcoach\Models\EmailList $emailList */
    public object $emailList;

    public Carbon $summaryStartDateTime;

    public string $emailListUrl;

    public function __construct(EmailList $emailList, Carbon $summaryStartDateTime)
    {
        $this->emailList = $emailList;

        $this->summaryStartDateTime = $summaryStartDateTime;

        $this->emailListUrl = route('mailcoach.emailLists.subscribers', $this->emailList);
    }

    public function build()
    {
        $this
            ->subject("A summary of the '{$this->emailList->name}' list")
            ->markdown('mailcoach::mails.emailListSummary', [
                'summary' => $this->emailList->summarize($this->summaryStartDateTime)
            ]);
    }
}
