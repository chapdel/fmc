<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\SendCampaignTimeLimitApproaching;
use Spatie\Mailcoach\Domain\Campaign\Jobs\CreateCampaignSendJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Mailcoach;

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
        $prepareSubjectAction = Mailcoach::getCampaignActionClass('prepare_subject', PrepareSubjectAction::class);

        $prepareSubjectAction->execute($campaign);

        return $this;
    }

    protected function prepareEmailHtml(Campaign $campaign): self
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\PrepareEmailHtmlAction $prepareEmailHtmlAction */
        $prepareEmailHtmlAction = Mailcoach::getCampaignActionClass('prepare_email_html', PrepareEmailHtmlAction::class);

        $prepareEmailHtmlAction->execute($campaign);

        return $this;
    }

    protected function prepareWebviewHtml(Campaign $campaign): self
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\PrepareWebviewHtmlAction $prepareWebviewHtmlAction */
        $prepareWebviewHtmlAction = Mailcoach::getCampaignActionClass('prepare_webview_html', PrepareWebviewHtmlAction::class);

        $prepareWebviewHtmlAction->execute($campaign);

        return $this;
    }

    protected function sendMailsForCampaign(Campaign $campaign, ?CarbonInterface $stopExecutingAt = null): void
    {
        $campaign->update(['segment_description' => $campaign->getSegment()->description()]);

        $subscribersQuery = $campaign->baseSubscribersQuery();

        $segment = $campaign->getSegment();

        $segment->subscribersQuery($subscribersQuery);

        if (is_null($campaign->sent_to_number_of_subscribers) || $campaign->sent_to_number_of_subscribers === 0) {
            $campaign->update(['sent_to_number_of_subscribers' => $subscribersQuery->count()]);
        }

        $this->dispatchCreateSendJobs($subscribersQuery, $campaign, $stopExecutingAt);

        if ($campaign->sends()->count() < $campaign->fresh()->sent_to_number_of_subscribers) {
            return;
        }

        $campaign->markAsAllSendsCreated();

        if ($campaign->sendsCount() < $campaign->sent_to_number_of_subscribers) {
            return;
        }

        $campaign->markAsSent($campaign->sends()->count());

        event(new CampaignSentEvent($campaign));
    }

    protected function dispatchCreateSendJobs(
        Builder $subscribersQuery,
        Campaign $campaign,
        CarbonInterface $stopExecutingAt = null,
    ): void {
        $subscribersQuery
            ->withoutSendsForCampaign($campaign)
            ->select('id')
            ->lazyById()
            ->each(function (Subscriber $subscriber) use ($stopExecutingAt, $campaign) {
                dispatch(new CreateCampaignSendJob($campaign, $subscriber));

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

        throw SendCampaignTimeLimitApproaching::make();
    }
}
