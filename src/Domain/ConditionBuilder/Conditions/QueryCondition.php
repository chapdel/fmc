<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Conditions;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Domain\ConditionBuilder\Traits\Conditionable;

abstract class QueryCondition implements Condition
{
    use Conditionable;

    abstract public function apply(Builder $baseQuery, ComparisonOperator $operator, mixed $value): Builder;
}
