<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\QueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ConditionCategory;

class SubscriberEmailQueryCondition extends QueryCondition
{
    public const KEY = 'subscriber_email';

    public function key(): string
    {
        return self::KEY;
    }

    public function comparisonOperators(): array
    {
        return [
            ComparisonOperator::StartsWith,
            ComparisonOperator::EndsWith,
        ];
    }

    public function category(): ConditionCategory
    {
        return ConditionCategory::Attributes;
    }

    public function getComponent(): string
    {
        return 'mailcoach::subscriber-email-condition';
    }

    public function apply(Builder $baseQuery, ComparisonOperator $operator, mixed $value): Builder
    {
        $this->ensureOperatorIsSupported($operator);

        if ($operator === ComparisonOperator::EndsWith) {
            return $baseQuery->where('email', 'like', "%{$value}");
        }

        return $baseQuery->where('email', 'like', "{$value}%");
    }

    public function dto(): ?string
    {
        return null;
    }
}
