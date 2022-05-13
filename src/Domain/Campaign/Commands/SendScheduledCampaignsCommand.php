<?php

namespace Spatie\Mailcoach\Domain\Campaign\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Campaign\Actions\SendCampaignAction;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\SendCampaignTimeLimitApproaching;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class SendScheduledCampaignsCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:send-scheduled-campaigns';

    public $description = 'Send scheduled campaigns.';

    public function handle()
    {
        $this->comment('Checking if there are scheduled campaigns that should be sent...');

        /** @var class-string<Campaign> $campaignClass */
        $campaignClass = $this->getCampaignClass();

        $campaignClass::shouldBeSentNow()
            ->each(function (Campaign $campaign) {
                $this->info("Sending campaign `{$campaign->name}` ({$campaign->id})...");
                $campaign->update(['scheduled_at' => null]);
                $campaign->send();
            });

        $this->comment('Checking if there are sending campaigns that need to create sends...');

        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\SendCampaignAction $sendCampaignAction */
        $sendCampaignAction = Config::getCampaignActionClass('send_campaign', SendCampaignAction::class);

        $maxRuntimeInSeconds = max(60, config('mailcoach.campaigns.send_campaign_maximum_job_runtime_in_seconds'));

        $campaignClass::sending()
            ->each(function (Campaign $campaign) use ($sendCampaignAction, $maxRuntimeInSeconds) {
                $stopExecutingAt = now()->addSeconds($maxRuntimeInSeconds);

                $this->info("Creating sends & dispatching sends for campaign `{$campaign->name}` ({$campaign->id})...");

                try {
                    $sendCampaignAction->execute($campaign, $stopExecutingAt);
                } catch (SendCampaignTimeLimitApproaching) {
                    return;
                }
            });

        $this->comment('All done!');
    }
}
