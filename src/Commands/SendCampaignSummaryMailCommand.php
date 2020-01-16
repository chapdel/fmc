<?php

namespace Spatie\Mailcoach\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Mails\CampaignSummaryMail;
use Spatie\Mailcoach\Models\Campaign;

class SendCampaignSummaryMailCommand extends Command
{
    public $signature = 'mailcoach:send-campaign-summary-mail';

    public $description = 'Send a summary mail to campaigns that have been sent out recently';

    public function handle()
    {
        Campaign::query()
            ->needsSummaryToBeReported()
            ->sentDaysAgo(1)
            ->get()
            ->each(function (Campaign $campaign) {
                Mail::to($campaign->emailList->campaignReportRecipients())->queue(new CampaignSummaryMail($campaign));

                $campaign->update(['summary_mail_sent_at' => now()]);

                $this->info("Summary mail sent for campaign `{$campaign->name}`");
            });

        $this->comment('All done!');
    }
}
