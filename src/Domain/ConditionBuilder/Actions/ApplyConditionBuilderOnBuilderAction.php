<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Actions;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\ConditionBuilder\Collections\StoredConditionCollection;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\QueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\ValueObjects\StoredCondition;

class ApplyConditionBuilderOnBuilderAction
{
    public function execute(Builder $builder, StoredConditionCollection $storedConditionCollection): Builder
    {
        $storedConditionCollection
            ->filter(fn (StoredCondition $storedCondition) => $storedCondition->condition instanceof QueryCondition)
            ->each(function (StoredCondition $storedCondition) use (&$builder) {
                /** @var QueryCondition $condition */
                $condition = $storedCondition->condition;

                $builder = $condition->apply(
                    baseQuery: $builder,
                    operator: $storedCondition->comparisonOperator,
                    value: $condition->dto()::fromArray($storedCondition->value),
                );
            });

        return $builder;
    }
}
