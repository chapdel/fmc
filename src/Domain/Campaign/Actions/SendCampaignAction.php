<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\SendCampaignTimeLimitApproaching;
use Spatie\Mailcoach\Domain\Campaign\Jobs\CreateCampaignSendJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Support\Throttling\SimpleThrottle;
use Spatie\Mailcoach\Mailcoach;

class SendCampaignAction
{
    public function execute(Campaign $campaign, CarbonInterface $stopExecutingAt = null): void
    {
        if (! $campaign->isSending()) {
            return;
        }

        $this
            ->prepareEmailHtml($campaign)
            ->prepareWebviewHtml($campaign)
            ->dispatchCreateSendJobs($campaign, $stopExecutingAt)
            ->markCampaignAsSent($campaign);
    }

    protected function prepareEmailHtml(Campaign $campaign): static
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\PrepareEmailHtmlAction $prepareEmailHtmlAction */
        $prepareEmailHtmlAction = Mailcoach::getCampaignActionClass('prepare_email_html', PrepareEmailHtmlAction::class);

        $prepareEmailHtmlAction->execute($campaign);

        return $this;
    }

    protected function prepareWebviewHtml(Campaign $campaign): static
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\PrepareWebviewHtmlAction $prepareWebviewHtmlAction */
        $prepareWebviewHtmlAction = Mailcoach::getCampaignActionClass('prepare_webview_html', PrepareWebviewHtmlAction::class);

        $prepareWebviewHtmlAction->execute($campaign);

        return $this;
    }

    protected function markCampaignAsSent(Campaign $campaign): void
    {
        $subscribersQueryCount = $this->getSubscribersQuery($campaign)->count();

        if ($subscribersQueryCount > $campaign->sendsCount()) {
            return;
        }

        if ($campaign->sends()->pending()->count()) {
            return;
        }

        $campaign->markAsSent($campaign->sendsCount());

        event(new CampaignSentEvent($campaign));
    }

    protected function dispatchCreateSendJobs(
        Campaign $campaign,
        CarbonInterface $stopExecutingAt = null,
    ): static {
        if ($campaign->allSendsCreated()) {
            return $this;
        }

        $campaign->update(['segment_description' => $campaign->getSegment()->description()]);

        $subscribersQuery = $this->getSubscribersQuery($campaign);

        $subscribersQueryCount = $subscribersQuery->count();

        $campaign->update(['sent_to_number_of_subscribers' => $subscribersQueryCount]);

        $simpleThrottle = app(SimpleThrottle::class)
            ->forMailerCreates($campaign->getMailerKey());

        $subscribersQuery
            ->withoutSendsForCampaign($campaign)
            ->lazyById()
            ->each(function (Subscriber $subscriber) use ($simpleThrottle, $stopExecutingAt, $campaign) {
                $this->haltWhenApproachingTimeLimit($stopExecutingAt, $simpleThrottle->sleepSeconds());

                $simpleThrottle->hit();

                dispatch(new CreateCampaignSendJob($campaign, $subscriber));
            });

        if ($subscribersQueryCount > $campaign->sends()->count()) {
            return $this;
        }

        $campaign->markAsAllSendsCreated();

        return $this;
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

    /** @return Builder<Subscriber> */
    protected function getSubscribersQuery(Campaign $campaign): Builder
    {
        $subscribersQuery = $campaign->baseSubscribersQuery();

        $segment = $campaign->getSegment();

        $segment->subscribersQuery($subscribersQuery);

        return $subscribersQuery;
    }
}
