<?php

namespace Spatie\Mailcoach\Domain\Campaign\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class RescueSendingCampaignsCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:rescue-sending-campaigns';

    public $description = 'Rescue sending campaigns.';

    public function handle()
    {
        $this->comment('Checking if there are campaigns that should be rescued...');

        /**
         * Dispatch the SendCampaignJob again for campaigns that have a status of sending
         * but haven't had any dispatched sends in a while, this is most likely because
         * the job failed dispatching itself again or Horizon was restarted.
         */
        Campaign::query()->where('status', CampaignStatus::SENDING)
            ->each(function (Campaign $campaign) {
                $latestDispatchedSend = $campaign->sends()
                    ->whereNotNull('sending_job_dispatched_at')
                    ->latest('sending_job_dispatched_at')
                    ->first();

                $latestCreatedSend = $campaign->sends()
                    ->whereNull('sending_job_dispatched_at')
                    ->latest('created_at')
                    ->first();

                // We'll take the timespan that is set to throttle + a minute to add some room for error
                $time = config('mailcoach.campaigns.throttling.timespan_in_seconds') + 60;

                $sendHasntBeenCreatedInTimeLimit = is_null($latestDispatchedSend) && $latestCreatedSend && $latestCreatedSend->created_at < now()->subSeconds($time);
                $sendHasntBeenDispatchedInTimeLimit = $latestDispatchedSend && $latestDispatchedSend->sending_job_dispatched_at < now()->subSeconds($time);

                if ($sendHasntBeenCreatedInTimeLimit || $sendHasntBeenDispatchedInTimeLimit) {
                    $this->comment("Dispatching a new job for campaign {$campaign->id}");
                    dispatch(new SendCampaignJob($campaign));
                }
            });

        $this->comment('All done!');
    }
}
