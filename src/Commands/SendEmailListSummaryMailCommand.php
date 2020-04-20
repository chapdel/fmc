<?php

namespace Spatie\Mailcoach\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Mails\EmailListSummaryMail;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class SendEmailListSummaryMailCommand extends Command
{
    use UsesMailcoachModels;

    protected $signature = 'mailcoach:send-email-list-summary-mail';

    public $description = 'Send a summary mail on the subscribers of a list';

    public function handle()
    {
        $this->getEmailListClass()::query()
            ->where('report_email_list_summary', true)
            ->each(
                function (EmailList $emailList) {
                    if (optional($emailList->email_list_summary_sent_at)->diffInDays() === 0) {
                        return;
                    }

                    $emailListSummaryMail = new EmailListSummaryMail(
                        $emailList,
                        $emailList->email_list_summary_sent_at ?? $emailList->created_at
                    );

                    Mail::mailer(config('mailcoach.mailer') ?? config('mail.default'))
                        ->to($emailList->campaignReportRecipients())
                        ->queue($emailListSummaryMail);

                    $emailList->update(['email_list_summary_sent_at' => now()]);
                }
            );

        $this->comment('All done!');
    }
}
