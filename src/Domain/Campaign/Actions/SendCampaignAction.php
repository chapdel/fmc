<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\Segment;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\SendCampaignTimeLimitApproaching;
use Spatie\Mailcoach\Domain\Campaign\Jobs\CreateCampaignSendJob;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Spatie\Mailcoach\Domain\Shared\Support\HorizonStatus;
use Spatie\Mailcoach\Domain\Shared\Support\Throttling\SimpleThrottle;

class SendCampaignAction
{
    public function execute(Campaign $campaign, ?CarbonInterface $stopExecutingAt = null): void
    {
        if ($campaign->wasAlreadySent() || ! $campaign->isSending()) {
            return;
        }

        $this
            ->prepareSubject($campaign)
            ->prepareEmailHtml($campaign)
            ->prepareWebviewHtml($campaign)
            ->sendMailsForCampaign($campaign, $stopExecutingAt);
    }

    protected function prepareSubject(Campaign $campaign): self
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\PrepareSubjectAction $prepareSubjectAction */
        $prepareSubjectAction = Config::getCampaignActionClass('prepare_subject', PrepareSubjectAction::class);

        $prepareSubjectAction->execute($campaign);

        return $this;
    }

    protected function prepareEmailHtml(Campaign $campaign): self
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\PrepareEmailHtmlAction $prepareEmailHtmlAction */
        $prepareEmailHtmlAction = Config::getCampaignActionClass('prepare_email_html', PrepareEmailHtmlAction::class);

        $prepareEmailHtmlAction->execute($campaign);

        return $this;
    }

    protected function prepareWebviewHtml(Campaign $campaign): self
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\PrepareWebviewHtmlAction $prepareWebviewHtmlAction */
        $prepareWebviewHtmlAction = Config::getCampaignActionClass('prepare_webview_html', PrepareWebviewHtmlAction::class);

        $prepareWebviewHtmlAction->execute($campaign);

        return $this;
    }

    protected function sendMailsForCampaign(Campaign $campaign, ?CarbonInterface $stopExecutingAt = null): void
    {
        $campaign->update(['segment_description' => $campaign->getSegment()->description()]);

        $subscribersQuery = $campaign->baseSubscribersQuery();

        $segment = $campaign->getSegment();

        $segment->subscribersQuery($subscribersQuery);

        if (is_null($campaign->sent_to_number_of_subscribers)) {
            $campaign->update(['sent_to_number_of_subscribers' => $subscribersQuery->count()]);
        }

        $this->dispatchCreateSendJobs($subscribersQuery, $campaign, $segment, $stopExecutingAt);

        if ($campaign->sends()->count() < $campaign->sent_to_number_of_subscribers) {
            return;
        }

        $campaign->markAsAllSendsCreated();

        if (! $campaign->allMailSendingJobsDispatched()) {
            $this->dispatchMailSendingJobs($campaign, $stopExecutingAt);
        }

        $campaign->markAsSent($campaign->sends()->count());

        event(new CampaignSentEvent($campaign));
    }

    protected function dispatchCreateSendJobs(
        Builder $subscribersQuery,
        Campaign $campaign,
        Segment  $segment,
        CarbonInterface   $stopExecutingAt = null,
    ): void {
        $subscribersQuery
            ->withoutSendsForCampaign($campaign)
            ->lazyById()
            ->each(function (Subscriber $subscriber) use ($stopExecutingAt, $campaign, $segment) {
                dispatch(new CreateCampaignSendJob($campaign, $subscriber, $segment));

                $this->haltWhenApproachingTimeLimit($stopExecutingAt);
            });
    }

    protected function dispatchMailSendingJobs(Campaign $campaign, CarbonInterface $stopExecutingAt = null): void
    {
        $simpleThrottle = app(SimpleThrottle::class)
            ->forMailer(config('mailcoach.campaigns.mailer'))
            ->allow(config('mailcoach.campaigns.throttling.allowed_number_of_jobs_in_timespan'))
            ->inSeconds(config('mailcoach.campaigns.throttling.timespan_in_seconds'));

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
