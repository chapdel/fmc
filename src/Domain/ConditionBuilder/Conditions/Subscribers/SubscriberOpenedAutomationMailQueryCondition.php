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

        if ($operator === ComparisonOperator::Any) {
            return $baseQuery->whereHas('automationMailOpens');
        }

        if ($operator === ComparisonOperator::None) {
            return $baseQuery->whereDoesntHave('automationMailOpens');
        }

        if (! is_string($value)) {
            throw ConditionException::unsupportedValue($value);
        }

        if ($operator === ComparisonOperator::NotEquals) {
            return $baseQuery
                ->whereHas('automationMailOpens.send.campaign', function (Builder $query) use ($value) {
                    $query->whereNot('name', $value);
                })->orWhereDoesntHave('automationMailOpens');
        }

        return $baseQuery->whereHas('automationMailOpens.send.campaign', function (Builder $query) use ($operator, $value) {
            $query->where('name', $operator->toSymbol(), $value);
        });
    }
}
