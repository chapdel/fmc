<?php

namespace Spatie\Mailcoach\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Models\Campaign;

class SendScheduledCampaignsCommand extends Command
{
    public $signature = 'mailcoach:send-scheduled-campaigns';

    public $description = 'Send scheduled campaigns.';

    public function handle()
    {
        $this->comment('Checking if there are scheduled campaigns that should be sent...');

        Campaign::shouldBeSentNow()
            ->each(function (Campaign $campaign) {
                $this->info("Sending campaign `{$campaign->name}` ({$campaign->id})...");
                $campaign->update(['scheduled_at' => null]);
                $campaign->send();
            });

        $this->comment('All done!');
    }
}
