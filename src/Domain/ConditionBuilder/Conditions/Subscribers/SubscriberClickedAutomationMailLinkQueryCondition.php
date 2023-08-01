<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\QueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ConditionCategory;
use Spatie\Mailcoach\Domain\ConditionBuilder\Exceptions\ConditionException;

class SubscriberClickedAutomationMailLinkQueryCondition extends QueryCondition
{
    public const KEY = 'subscriber_clicked_automation_mail_link';

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
        return 'mailcoach::subscriber-clicked-automation-mail-link-condition';
    }

    public function apply(Builder $baseQuery, ComparisonOperator $operator, mixed $value): Builder
    {
        $this->ensureOperatorIsSupported($operator);

        // @todo performance issues ?
        if ($operator === ComparisonOperator::Any) {
            return $baseQuery->whereHas('sends.automationMail.links');
        }

        if ($operator === ComparisonOperator::None) {
            return $baseQuery->whereDoesntHave('sends.automationMail.links');
        }

        if (! is_string($value)) {
            throw ConditionException::unsupportedValue($value);
        }

        if ($operator === ComparisonOperator::NotEquals) {
            return $baseQuery
                ->whereHas('sends.automationMail.links', function (Builder $query) use ($value) {
                    $query->whereNot('url', $value);
                })->orWhereDoesntHave('sends.automationMail.links');
        }

        return $baseQuery->whereHas('sends.automationMail.links', function (Builder $query) use ($operator, $value) {
            $query->where('url', $operator->toSymbol(), $value);
        });

        $this->ensureOperatorIsSupported($operator);

        if (! is_numeric($value) && ! is_bool($value)) {
            throw ConditionException::unsupportedValue($value);
        }

        return $value ? $baseQuery->has('clicks') : $baseQuery->doesntHave('clicks');
    }
}
