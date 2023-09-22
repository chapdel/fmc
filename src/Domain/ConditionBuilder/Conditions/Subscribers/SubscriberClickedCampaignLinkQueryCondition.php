<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\QueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ConditionCategory;
use Spatie\Mailcoach\Domain\ConditionBuilder\Exceptions\ConditionException;

class SubscriberClickedCampaignLinkQueryCondition extends QueryCondition
{
    public const KEY = 'subscriber_clicked_campaign_link';

    public function key(): string
    {
        return self::KEY;
    }

    public function comparisonOperators(): array
    {
        return [
            ComparisonOperator::Any,
            ComparisonOperator::None,
            ComparisonOperator::Equals,
            ComparisonOperator::NotEquals,
        ];
    }

    public function category(): ConditionCategory
    {
        return ConditionCategory::Actions;
    }

    public function getComponent(): string
    {
        return 'mailcoach::subscriber-clicked-campaign-link-condition';
    }

    public function apply(Builder $baseQuery, ComparisonOperator $operator, mixed $value): Builder
    {
        $this->ensureOperatorIsSupported($operator);

        $campaignClass = self::getCampaignClass();
        $campaignMorphClass = (new $campaignClass)->getMorphClass();

        // @todo performance issues ?
        if ($operator === ComparisonOperator::Any) {
            return $baseQuery->whereHas('clicks.link.contentItem', function (Builder $query) use ($campaignMorphClass) {
                $query->where('model_type', $campaignMorphClass);
            });
        }

        if ($operator === ComparisonOperator::None) {
            return $baseQuery->whereDoesntHave('clicks.link.contentItem', function (Builder $query) use ($campaignMorphClass) {
                $query->where('model_type', $campaignMorphClass);
            });
        }

        if (! is_string($value)) {
            throw ConditionException::unsupportedValue($value);
        }

        if ($operator === ComparisonOperator::NotEquals) {
            return $baseQuery
                ->whereHas('clicks.link', function (Builder $query) use ($campaignMorphClass, $value) {
                    $query->whereNot('url', $value);
                    $query->whereHas('contentItem', function (Builder $query) use ($campaignMorphClass) {
                        $query->where('model_type', $campaignMorphClass);
                    });
                })
                ->orWhereDoesntHave('clicks.link.contentItem', function (Builder $query) use ($campaignMorphClass) {
                    $query->where('model_type', $campaignMorphClass);
                });
        }

        return $baseQuery
            ->whereHas('clicks.link', function (Builder $query) use ($operator, $campaignMorphClass, $value) {
                $query->where('url', $operator->toSymbol(), $value);
                $query->whereHas('contentItem', function (Builder $query) use ($campaignMorphClass) {
                    $query->where('model_type', $campaignMorphClass);
                });
            });
    }
}
