<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Carbon\CarbonInterface;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\SendCampaignTimeLimitApproaching;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Support\HorizonStatus;
use Spatie\Mailcoach\Domain\Shared\Support\Throttling\SimpleThrottle;

class SendCampaignMailsAction
{
    public function execute(Campaign $campaign, CarbonInterface $stopExecutingAt = null): void
    {
        $this->retryDispatchForStuckSends($campaign);

        if ($campaign->allMailSendingJobsDispatched()) {
            return;
        }

        if (! $campaign->sends()->undispatched()->count()) {
            if ($campaign->allSendsCreated()) {
                $campaign->markAsAllMailSendingJobsDispatched();
            }

            return;
        }

        $this->dispatchMailSendingJobs($campaign, $stopExecutingAt);
    }

    /**
     * Dispatch pending sends again that have
     * not been processed in a realistic time
     */
    protected function retryDispatchForStuckSends(Campaign $campaign): void
    {
        $realisticTimeInMinutes = round($campaign->sent_to_number_of_subscribers / 10 / 60);

        $retryQuery = $campaign->sends()
            ->pending()
            ->where('sending_job_dispatched_at', '<', now()->subMinutes($realisticTimeInMinutes * 2));

        if ($retryQuery->count() === 0) {
            return;
        }

        $campaign->update(['all_sends_dispatched_at' => null]);

        $retryQuery->each(function (Send $send) {
            dispatch(new SendCampaignMailJob($send));

            $send->markAsSendingJobDispatched();
        });
    }

    protected function dispatchMailSendingJobs(Campaign $campaign, CarbonInterface $stopExecutingAt = null): void
    {
        $simpleThrottle = app(SimpleThrottle::class)
            ->forMailer($campaign->getMailerKey());

        $undispatchedCount = $campaign->sends()->undispatched()->count();

        while ($undispatchedCount > 0) {
            $campaign
                ->sends()
                ->undispatched()
                ->lazyById()
                ->each(function (Send $send) use ($stopExecutingAt, $simpleThrottle) {
                    // should horizon be used, and it is paused, stop dispatching jobs
                    if (! app(HorizonStatus::class)->is(HorizonStatus::STATUS_PAUSED)) {
                        $simpleThrottle->hit();

                        dispatch(new SendCampaignMailJob($send));

                        $send->markAsSendingJobDispatched();
                    }

                    $this->haltWhenApproachingTimeLimit($stopExecutingAt);
                });

            $undispatchedCount = $campaign->sends()->undispatched()->count();
        }

        if (! $campaign->allSendsCreated()) {
            return;
        }

        $campaign->markAsAllMailSendingJobsDispatched();
    }

    protected function haltWhenApproachingTimeLimit(?CarbonInterface $stopExecutingAt): void
    {
        if (is_null($stopExecutingAt)) {
            return;
        }

        if ($stopExecutingAt->diffInSeconds() > 30) {
            return;
        }

        throw SendCampaignTimeLimitApproaching::make();
    }
}
