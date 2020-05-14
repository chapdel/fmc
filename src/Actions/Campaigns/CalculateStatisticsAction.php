<?php

namespace Spatie\Mailcoach\Actions\Campaigns;

use Spatie\Mailcoach\Events\CampaignStatisticsCalculatedEvent;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\CampaignLink;

class CalculateStatisticsAction
{
    public function execute(Campaign $campaign)
    {
        if ($campaign->sends()->count() > 0) {
            $this
                ->calculateCampaignStatistics($campaign)
                ->calculateLinkStatistics($campaign);
        }

        $campaign->update(['statistics_calculated_at' => now()]);

        event(new CampaignStatisticsCalculatedEvent($campaign));
    }

    protected function calculateCampaignStatistics(Campaign $campaign): self
    {
        $sentToNumberOfSubscribers = $campaign->sends()->count();

        [$openCount, $uniqueOpenCount, $openRate] = $this->calculateOpenMetrics($campaign, $sentToNumberOfSubscribers);
        [$clickCount, $uniqueClickCount, $clickRate] = $this->calculateClickMetrics($campaign, $sentToNumberOfSubscribers);
        [$unsubscribeCount, $unsubscribeRate] = $this->calculateUnsubscribeMetrics($campaign, $sentToNumberOfSubscribers);
        [$bounceCount, $bounceRate] = $this->calculateBounceMetrics($campaign, $sentToNumberOfSubscribers);

        $campaign->update([
            'sent_to_number_of_subscribers' => $sentToNumberOfSubscribers,
            'open_count' => $openCount,
            'unique_open_count' => $uniqueOpenCount,
            'open_rate' => $openRate,
            'click_count' => $clickCount,
            'unique_click_count' => $uniqueClickCount,
            'click_rate' => $clickRate,
            'unsubscribe_count' => $unsubscribeCount,
            'unsubscribe_rate' => $unsubscribeRate,
            'bounce_count' => $bounceCount,
            'bounce_rate' => $bounceRate,
        ]);

        return $this;
    }

    protected function calculateLinkStatistics(Campaign $campaign): self
    {
        $campaign->links->each(function (CampaignLink $link) {
            $link->update([
                'click_count' => $link->clicks()->count(),
                'unique_click_count' => $link->clicks()->select('subscriber_id')->groupBy('subscriber_id')->toBase()->select('subscriber_id')->getCountForPagination(['subscriber_id']),
            ]);
        });

        return $this;
    }

    protected function calculateClickMetrics(Campaign $campaign, int $sendToNumberOfSubscribers): array
    {
        $clickCount = $campaign->clicks()->count();
        $uniqueClickCount = $campaign->clicks()->groupBy('subscriber_id')->toBase()->select('subscriber_id')->getCountForPagination(['subscriber_id']);
        $clickRate = round($uniqueClickCount / $sendToNumberOfSubscribers, 2) * 100;

        return [$clickCount, $uniqueClickCount, $clickRate];
    }

    protected function calculateOpenMetrics(Campaign $campaign, int $sendToNumberOfSubscribers): array
    {
        $openCount = $campaign->opens()->count();
        $uniqueOpenCount = $campaign->opens()->groupBy('subscriber_id')->toBase()->select('subscriber_id')->getCountForPagination(['subscriber_id']);
        $openRate = round($uniqueOpenCount / $sendToNumberOfSubscribers, 2) * 100;

        return [$openCount, $uniqueOpenCount, $openRate];
    }

    protected function calculateUnsubscribeMetrics(Campaign $campaign, int $sendToNumberOfSubscribers): array
    {
        $unsubscribeCount = $campaign->unsubscribes()->count();
        $unsubscribeRate = round($unsubscribeCount / $sendToNumberOfSubscribers, 2) * 100;

        return [$unsubscribeCount, $unsubscribeRate];
    }

    protected function calculateBounceMetrics(Campaign $campaign, int $sendToNumberOfSubscribers): array
    {
        $bounceCount = $campaign->bounces()->count();
        $bounceRate = round($bounceCount / $sendToNumberOfSubscribers, 2) * 100;

        return [$bounceCount, $bounceRate];
    }
}
