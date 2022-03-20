<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Laravel\Horizon\Horizon;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\Segment;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\SendCampaignTimeLimitApproaching;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignJob;
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
        if ($campaign->wasAlreadySent()) {
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

    protected function sendMailsForCampaign(Campaign $campaign, ?CarbonInterface $stopExecutingAt = null): self
    {
        $campaign->update(['segment_description' => $campaign->getSegment()->description()]);

        $subscribersQuery = $campaign->baseSubscribersQuery();

        $segment = $campaign->getSegment();

        $segment->subscribersQuery($subscribersQuery);

        $campaign->update(['sent_to_number_of_subscribers' => $subscribersQuery->count()]);

        try {
            if (! $campaign->allSendsCreated()) {
                $this->createSends($subscribersQuery, $campaign, $segment, $stopExecutingAt);
            }

            if (! $campaign->allMailSendingJobsDispatched()) {
                $this->dispatchMailSendingJobs($campaign, $stopExecutingAt);
            }

            $campaign->markAsSent($campaign->sends()->count());

            event(new CampaignSentEvent($campaign));
        } catch (SendCampaignTimeLimitApproaching) {
            dispatch(new SendCampaignJob($campaign))->onQueue(config('mailcoach.campaigns.perform_on_queue.send_campaign_job'));
        }

        return $this;
    }

    protected function createSend(Campaign $campaign, EmailList $emailList, Subscriber $subscriber, Segment $segment = null): void
    {
        if ($segment && ! $segment->shouldSend($subscriber)) {
            $campaign->decrement('sent_to_number_of_subscribers');

            return;
        }

        if (! $this->isValidSubscriptionForEmailList($subscriber, $emailList)) {
            $campaign->decrement('sent_to_number_of_subscribers');

            return;
        }

        /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $pendingSend */
        $pendingSend = $campaign->sends()
            ->where('subscriber_id', $subscriber->id)
            ->first();

        if ($pendingSend) {
            return;
        }

        $campaign->sends()->create([
            'subscriber_id' => $subscriber->id,
            'uuid' => (string)Str::uuid(),
        ]);
    }

    protected function isValidSubscriptionForEmailList(Subscriber $subscriber, EmailList $emailList): bool
    {
        if (! $subscriber->isSubscribed()) {
            return false;
        }

        if ((int)$subscriber->email_list_id !== (int)$emailList->id) {
            return false;
        }

        return true;
    }

    protected function createSends(
        Builder  $subscribersQuery,
        Campaign $campaign,
        Segment  $segment,
        CarbonInterface   $stopExecutingAt = null,
    ): void {
        $subscribersQuery
            ->whereDoesntHave('sends', function (Builder $query) use ($campaign) {
                $query->where('campaign_id', $campaign->id);
            })
            ->lazyById()
            ->each(function (Subscriber $subscriber) use ($stopExecutingAt, $campaign, $segment) {
                $this->createSend($campaign, $campaign->emailList, $subscriber, $segment);

                $this->haltWhenApproachingTimeLimit($stopExecutingAt);
            });

        $campaign->markAsAllSendsCreated();
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
