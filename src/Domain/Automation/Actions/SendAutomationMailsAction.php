<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Carbon\CarbonInterface;
use Spatie\Mailcoach\Domain\Automation\Exceptions\SendAutomationMailsTimeLimitApproaching;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailJob;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Support\HorizonStatus;
use Spatie\Mailcoach\Domain\Shared\Support\Throttling\SimpleThrottle;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class SendAutomationMailsAction
{
    use UsesMailcoachModels;

    public function execute(?CarbonInterface $stopExecutingAt = null)
    {
        $simpleThrottle = app(SimpleThrottle::class)
            ->forMailer(config('mailcoach.automation.mailer'))
            ->allow(config('mailcoach.automation.throttling.allowed_number_of_jobs_in_timespan'))
            ->inSeconds(config('mailcoach.automation.throttling.timespan_in_seconds'));

        self::getSendClass()::query()
            ->undispatched()
            ->whereNotNull('automation_mail_id')
            ->lazyById()
            ->each(function (Send $send) use ($stopExecutingAt, $simpleThrottle) {
                // should horizon be used, and it is paused, stop dispatching jobs
                if (! app(HorizonStatus::class)->is(HorizonStatus::STATUS_PAUSED)) {
                    $simpleThrottle->hit();

                    dispatch(new SendAutomationMailJob($send));

                    $send->markAsSendingJobDispatched();
                }

                $this->haltWhenApproachingTimeLimit($stopExecutingAt);
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
