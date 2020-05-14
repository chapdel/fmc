<?php

namespace Spatie\Mailcoach\Mails;

use Carbon\CarbonInterface;
use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Models\EmailList;

class EmailListSummaryMail extends Mailable
{
    public string $theme = 'mailcoach::mails.layout.mailcoach';

    /** @var \Spatie\Mailcoach\Models\EmailList */
    public object $emailList;

    public CarbonInterface $summaryStartDateTime;

    public string $emailListUrl;

    public function __construct(EmailList $emailList, CarbonInterface $summaryStartDateTime)
    {
        $this->emailList = $emailList;

        $this->summaryStartDateTime = $summaryStartDateTime;

        $this->emailListUrl = route('mailcoach.emailLists.subscribers', $this->emailList);
    }

    public function build()
    {
        $this
            ->from(
                $this->emailList->default_from_email,
                $this->emailList->default_from_name
            )
            ->subject("A summary of the '{$this->emailList->name}' list")
            ->markdown('mailcoach::mails.emailListSummary', [
                'summary' => $this->emailList->summarize($this->summaryStartDateTime),
            ]);
    }
}
