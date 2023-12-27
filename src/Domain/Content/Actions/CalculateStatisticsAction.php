<?php

namespace Spatie\Mailcoach\Domain\Content\Actions;

use Spatie\Mailcoach\Domain\Automation\Events\AutomationMailStatisticsCalculatedEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignStatisticsCalculatedEvent;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Content\Models\Link;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CalculateStatisticsAction
{
    use UsesMailcoachModels;

    public function execute(ContentItem $contentItem): void
    {
        if ($contentItem->sends()->count() > 0) {
            $this
                ->calculateStatistics($contentItem)
                ->calculateLinkStatistics($contentItem);
        }

        $contentItem->update(['statistics_calculated_at' => now()]);
        $contentItem->fresh('model');

        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign|\Spatie\Mailcoach\Domain\Automation\Models\AutomationMail $model */
        if (! $model = $contentItem->getModel()) {
            return;
        }

        match (true) {
            $model instanceof (static::getCampaignClass()) => event(new CampaignStatisticsCalculatedEvent($model)),
            $model instanceof (static::getAutomationMailClass()) => event(new AutomationMailStatisticsCalculatedEvent($model)),
            default => null,
        };
    }

    protected function calculateStatistics(ContentItem $contentItem): self
    {
        $sentToNumberOfSubscribers = $contentItem->sends()->count();

        [$openCount, $uniqueOpenCount, $openRate] = $this->calculateOpenMetrics($contentItem, $sentToNumberOfSubscribers);
        [$clickCount, $uniqueClickCount, $clickRate] = $this->calculateClickMetrics($contentItem, $sentToNumberOfSubscribers);
        [$unsubscribeCount, $unsubscribeRate] = $this->calculateUnsubscribeMetrics($contentItem, $sentToNumberOfSubscribers);
        [$bounceCount, $bounceRate] = $this->calculateBounceMetrics($contentItem, $sentToNumberOfSubscribers);

        $contentItem->fill([
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

        if (! $contentItem->model instanceof (self::getCampaignClass())) {
            $contentItem->sent_to_number_of_subscribers = $sentToNumberOfSubscribers;
        }

        $contentItem->save();

        return $this;
    }

    protected function calculateLinkStatistics(ContentItem $contentItem): self
    {
        $contentItem->links()->each(function (Link $link) {
            $tableName = static::getClickTableName();

            $link->update([
                'click_count' => $link->clicks()->count(),
                'unique_click_count' => $link->clicks()->select("{$tableName}.subscriber_id")->groupBy("{$tableName}.subscriber_id")->toBase()->select("{$tableName}.subscriber_id")->getCountForPagination(['subscriber_id']),
            ]);
        });

        return $this;
    }

    protected function calculateClickMetrics(ContentItem $contentItem, int $sendToNumberOfSubscribers): array
    {
        $tableName = static::getClickTableName();

        $clickCount = $contentItem->clicks()->count();
        $uniqueClickCount = $contentItem->clicks()->groupBy("{$tableName}.subscriber_id")->toBase()->select("{$tableName}.subscriber_id")->getCountForPagination(['subscriber_id']);
        $clickRate = round($uniqueClickCount / $sendToNumberOfSubscribers, 4) * 10000;

        return [$clickCount, $uniqueClickCount, $clickRate];
    }

    protected function calculateOpenMetrics(ContentItem $contentItem, int $sendToNumberOfSubscribers): array
    {
        $tableName = static::getOpenTableName();

        $openCount = $contentItem->opens()->count();
        $uniqueOpenCount = $contentItem->opens()->groupBy("{$tableName}.subscriber_id")->toBase()->select("{$tableName}.subscriber_id")->getCountForPagination(['subscriber_id']);
        $openRate = round($uniqueOpenCount / $sendToNumberOfSubscribers, 4) * 10000;

        return [$openCount, $uniqueOpenCount, $openRate];
    }

    protected function calculateUnsubscribeMetrics(ContentItem $contentItem, int $sendToNumberOfSubscribers): array
    {
        $unsubscribeCount = $contentItem->unsubscribes()->count();
        $unsubscribeRate = round($unsubscribeCount / $sendToNumberOfSubscribers, 4) * 10000;

        return [$unsubscribeCount, $unsubscribeRate];
    }

    protected function calculateBounceMetrics(ContentItem $contentItem, int $sendToNumberOfSubscribers): array
    {
        $bounceCount = $contentItem->bounces()->distinct('send_id')->count();
        $bounceRate = round($bounceCount / $sendToNumberOfSubscribers, 4) * 10000;

        return [$bounceCount, $bounceRate];
    }
}
