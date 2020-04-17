<?php

namespace Spatie\Mailcoach\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Traits\UsesCampaign;

class SendScheduledCampaignsCommand extends Command
{
    use UsesCampaign;

    public $signature = 'mailcoach:send-scheduled-campaigns';

    public $description = 'Send scheduled campaigns.';

    public function handle()
    {
        $this->comment('Checking if there are scheduled campaigns that should be sent...');

        $this->getCampaignClass()::shouldBeSentNow()
            ->each(function (Campaig $campaign) {
                $this->info("Sending campaign `{$campaign->name}` ({$campaign->id})...");
                $campaign->update(['scheduled_at' => null]);
                $campaign->send();
            });

        $this->comment('All done!');
    }
}
