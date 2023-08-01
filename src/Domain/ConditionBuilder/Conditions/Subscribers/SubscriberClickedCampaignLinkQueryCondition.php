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

        if ($operator === ComparisonOperator::Any) {
            return $baseQuery->whereHas('clicks.link');
        }

        if ($operator === ComparisonOperator::None) {
            return $baseQuery->whereDoesntHave('clicks.link');
        }

        if (! is_string($value)) {
            throw ConditionException::unsupportedValue($value);
        }

        if ($operator === ComparisonOperator::NotEquals) {
            return $baseQuery
                ->whereHas('clicks.link', function (Builder $query) use ($value) {
                    $query->whereNot('url', $value);
                })->orWhereDoesntHave('clicks.link');
        }

        return $baseQuery->whereHas('clicks.link', function (Builder $query) use ($operator, $value) {
            $query->where('url', $operator->toSymbol(), $value);
        });
    }
}
