<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\SendCampaignTimeLimitApproaching;
use Spatie\Mailcoach\Domain\Campaign\Jobs\CreateCampaignSendJob;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Support\HorizonStatus;
use Spatie\Mailcoach\Domain\Shared\Support\Throttling\SimpleThrottle;
use Spatie\Mailcoach\Mailcoach;

class SendCampaignMailsAction
{
    public function execute(Campaign $campaign, ?CarbonInterface $stopExecutingAt = null): void
    {
        if ($campaign->wasAlreadySent() || ! $campaign->isSending()) {
            return;
        }

        if ($campaign->allMailSendingJobsDispatched()) {
            return;
        }

        $this->dispatchMailSendingJobs($campaign, $stopExecutingAt);
    }

    protected function dispatchMailSendingJobs(Campaign $campaign, CarbonInterface $stopExecutingAt = null): void
    {
        $simpleThrottle = app(SimpleThrottle::class)
            ->forMailer($campaign->getMailerKey());

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
