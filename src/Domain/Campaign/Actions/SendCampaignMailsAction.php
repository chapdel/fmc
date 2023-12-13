<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Carbon\CarbonInterface;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\SendCampaignTimeLimitApproaching;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Support\HorizonStatus;
use Spatie\Mailcoach\Domain\Shared\Support\Throttling\SimpleThrottle;

class SendCampaignMailsAction
{
    public function execute(Campaign $campaign, ?CarbonInterface $stopExecutingAt = null): void
    {
        foreach ($campaign->contentItems as $contentItem) {
            $this->retryDispatchForStuckSends($contentItem, $stopExecutingAt);

            if (! $contentItem->sends()->undispatched()->count()) {
                if ($contentItem->allSendsCreated() && ! $contentItem->allMailSendingJobsDispatched()) {
                    $contentItem->markAsAllMailSendingJobsDispatched();
                }

                continue;
            }

            $this->dispatchMailSendingJobs($contentItem, $stopExecutingAt);
        }
    }

    /**
     * Dispatch pending sends again that have
     * not been processed in a realistic time
     */
    protected function retryDispatchForStuckSends(ContentItem $contentItem, ?CarbonInterface $stopExecutingAt = null): void
    {
        $mailer = $contentItem->getMailerKey();
        $mailsPerTimespan = config("mail.mailers.{$mailer}.mails_per_timespan", 10);
        $timespan = config("mail.mailers.{$mailer}.timespan_in_seconds", 1);
        $mailsPerSecond = $mailsPerTimespan / $timespan;

        $realisticTimeInMinutes = min(
            60 * 3, // SendCampaignMailJob only has 3 hours retryUntil()
            round($contentItem->sent_to_number_of_subscribers / $mailsPerSecond / 60),
        );

        $retryQuery = $contentItem
            ->sends()
            ->pending()
            ->where('sending_job_dispatched_at', '<', now()->subMinutes($realisticTimeInMinutes + 15));

        if ($retryQuery->count() === 0) {
            return;
        }

        $contentItem->update(['all_sends_dispatched_at' => null]);

        $simpleThrottle = app(SimpleThrottle::class)
            ->forMailer($contentItem->getMailerKey());

        $retryQuery->each(function (Send $send) use ($stopExecutingAt, $simpleThrottle) {
            $this->haltWhenApproachingTimeLimit($stopExecutingAt, $simpleThrottle->sleepSeconds());

            $simpleThrottle->hit();

            dispatch(new SendCampaignMailJob($send));

            $send->markAsSendingJobDispatched();
        });
    }

    protected function dispatchMailSendingJobs(ContentItem $contentItem, ?CarbonInterface $stopExecutingAt = null): void
    {
        $simpleThrottle = app(SimpleThrottle::class)
            ->forMailer($contentItem->getMailerKey());

        $undispatchedCount = $contentItem->sends()->undispatched()->count();

        while ($undispatchedCount > 0) {
            $contentItem
                ->sends()
                ->undispatched()
                ->lazyById()
                ->each(function (Send $send) use ($stopExecutingAt, $simpleThrottle) {
                    $this->haltWhenApproachingTimeLimit($stopExecutingAt, $simpleThrottle->sleepSeconds());

                    // should horizon be used, and it is paused, stop dispatching jobs
                    if (! app(HorizonStatus::class)->is(HorizonStatus::STATUS_PAUSED)) {
                        $simpleThrottle->hit();

                        dispatch(new SendCampaignMailJob($send));

                        $send->markAsSendingJobDispatched();
                    }
                });

            $undispatchedCount = $contentItem->sends()->undispatched()->count();
        }

        if (! $contentItem->allSendsCreated()) {
            return;
        }

        $contentItem->markAsAllMailSendingJobsDispatched();
    }

    protected function haltWhenApproachingTimeLimit(?CarbonInterface $stopExecutingAt, int $sleepSeconds = 0): void
    {
        if (is_null($stopExecutingAt)) {
            return;
        }

        if ($stopExecutingAt->diffInSeconds() - $sleepSeconds > 10) {
            return;
        }

        throw SendCampaignTimeLimitApproaching::make();
    }
}
