<?php

namespace Spatie\Mailcoach\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Mails\CampaignSummaryMail;
use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;
use Spatie\Mailcoach\Traits\UsesCampaign;

class SendCampaignSummaryMailCommand extends Command
{
    use UsesCampaign;

    public $signature = 'mailcoach:send-campaign-summary-mail';

    public $description = 'Send a summary mail to campaigns that have been sent out recently';

    public function handle()
    {
        $this->getCampaignClass()::query()
            ->needsSummaryToBeReported()
            ->sentDaysAgo(1)
            ->get()
            ->each(function (CampaignConcern $campaign) {
                Mail::mailer(config('mailcoach.mailer') ?? config('mail.default'))
                    ->to($campaign->emailList->campaignReportRecipients())
                    ->queue(new CampaignSummaryMail($campaign));

                $campaign->update(['summary_mail_sent_at' => now()]);

                $this->info("Summary mail sent for campaign `{$campaign->name}`");
            });

        $this->comment('All done!');
    }
}
