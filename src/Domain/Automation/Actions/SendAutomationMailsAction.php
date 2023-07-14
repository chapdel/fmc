<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Carbon\CarbonInterface;
use Spatie\Mailcoach\Domain\Automation\Exceptions\SendAutomationMailsTimeLimitApproaching;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailJob;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Support\HorizonStatus;
use Spatie\Mailcoach\Domain\Shared\Support\Throttling\SimpleThrottle;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class SendAutomationMailsAction
{
    use UsesMailcoachModels;

    public function __construct(private SimpleThrottle $throttle)
    {
    }

    public function execute(CarbonInterface $stopExecutingAt = null)
    {
        $this->retryDispatchForStuckSends();

        self::getSendClass()::query()
            ->undispatched()
            ->whereNotNull('automation_mail_id')
            ->lazyById()
            ->each(function (Send $send) use ($stopExecutingAt) {
                $this->throttle->forMailer($send->subscriber->emailList->automation_mailer ?? Mailcoach::defaultAutomationMailer());

                // should horizon be used, and it is paused, stop dispatching jobs
                if (! app(HorizonStatus::class)->is(HorizonStatus::STATUS_PAUSED)) {
                    $this->throttle->hit();

                    dispatch(new SendAutomationMailJob($send));

                    $send->markAsSendingJobDispatched();
                }

                $this->haltWhenApproachingTimeLimit($stopExecutingAt);
            });
    }

    /**
     * Dispatch pending sends again that have
     * not been processed in the 30 minutes
     */
    protected function retryDispatchForStuckSends(): void
    {
        $retryQuery = self::getSendClass()::query()
            ->whereNotNull('automation_mail_id')
            ->pending()
            ->where('sending_job_dispatched_at', '<', now()->subMinutes(30));

        if ($retryQuery->count() === 0) {
            return;
        }

        $retryQuery->each(function (Send $send) {
            dispatch(new SendAutomationMailJob($send));

            $send->markAsSendingJobDispatched();
        });
    }

    protected function haltWhenApproachingTimeLimit(?CarbonInterface $stopExecutingAt): void
    {
        if (is_null($stopExecutingAt)) {
            return;
        }

        if ($stopExecutingAt->diffInSeconds() > 30) {
            return;
        }

        throw SendAutomationMailsTimeLimitApproaching::make();
    }
}
