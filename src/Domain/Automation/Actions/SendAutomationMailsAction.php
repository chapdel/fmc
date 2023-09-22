<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Carbon\CarbonInterface;
use Illuminate\Contracts\Database\Eloquent\Builder;
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

    public function execute(CarbonInterface $stopExecutingAt = null): void
    {
        $this->retryDispatchForStuckSends();

        self::getSendClass()::query()
            ->undispatched()
            ->whereHas('contentItem', function (Builder $query) {
                /** @var \Spatie\Mailcoach\Domain\Automation\Models\AutomationMail $automationMail */
                $automationMail = new (self::getAutomationMailClass());
                $query->where('model_type', $automationMail->getMorphClass());
            })
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
            ->whereHas('contentItem', function (Builder $query) {
                /** @var \Spatie\Mailcoach\Domain\Automation\Models\AutomationMail $automationMail */
                $automationMail = new (self::getAutomationMailClass());
                $query->where('model_type', $automationMail->getMorphClass());
            })
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
