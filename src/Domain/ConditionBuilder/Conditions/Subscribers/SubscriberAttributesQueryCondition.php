<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\QueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ConditionCategory;

class SubscriberAttributesQueryCondition extends QueryCondition
{
    public function key(): string
    {
        return 'subscriber_attributes';
    }

    public function comparisonOperators(): array
    {
        return [
            ComparisonOperator::In,
            ComparisonOperator::NotIn,
        ];
    }

    public function category(): ConditionCategory
    {
        return ConditionCategory::Attributes;
    }

    public function getComponent(): string
    {
        return 'mailcoach::subscriber-attributes-condition';
    }

    public function apply(Builder $baseQuery, ComparisonOperator $operator, mixed $value): Builder
    {
        // @todo
        return $baseQuery;
    }
}
