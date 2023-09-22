<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\QueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ConditionCategory;
use Spatie\Mailcoach\Domain\ConditionBuilder\Exceptions\ConditionException;

class SubscriberOpenedAutomationMailQueryCondition extends QueryCondition
{
    public const KEY = 'subscriber_opened_automation_mail';

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
        return 'mailcoach::subscriber-opened-automation-mail-condition';
    }

    public function apply(Builder $baseQuery, ComparisonOperator $operator, mixed $value): Builder
    {
        $this->ensureOperatorIsSupported($operator);

        $automationMailClass = self::getAutomationMailClass();
        $automationMailMorphClass = (new $automationMailClass)->getMorphClass();

        if ($operator === ComparisonOperator::Any) {
            return $baseQuery->whereHas('opens.contentItem', function (Builder $query) use ($automationMailMorphClass) {
                $query->where('model_type', $automationMailMorphClass);
            });
        }

        if ($operator === ComparisonOperator::None) {
            return $baseQuery->whereDoesntHave('opens.contentItem', function (Builder $query) use ($automationMailMorphClass) {
                $query->where('model_type', $automationMailMorphClass);
            });
        }

        if (! is_string($value)) {
            throw ConditionException::unsupportedValue($value);
        }

        if ($operator === ComparisonOperator::NotEquals) {
            return $baseQuery
                ->whereHas('opens.contentItem', function (Builder $query) use ($automationMailMorphClass, $value) {
                    $query->where('model_type', $automationMailMorphClass)
                        ->whereHas('model', function (Builder $query) use ($value) {
                            $query->whereNot('name', $value);
                        });
                })->orWhereDoesntHave('opens.contentItem', function (Builder $query) use ($automationMailMorphClass) {
                    $query->where('model_type', $automationMailMorphClass);
                });
        }

        return $baseQuery->whereHas('opens.contentItem', function (Builder $query) use ($automationMailMorphClass, $operator, $value) {
            $query->where('model_type', $automationMailMorphClass)
                ->whereHas('model', function (Builder $query) use ($operator, $value) {
                    $query->where('name', $operator->toSymbol(), $value);
                });
        });
    }
}
