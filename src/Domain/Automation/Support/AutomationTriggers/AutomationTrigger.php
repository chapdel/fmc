<?php


namespace Spatie\Mailcoach\Domain\Automation\Support\AutomationTriggers;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\AutomationStep;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

abstract class AutomationTrigger extends AutomationStep
{
    use UsesMailcoachModels;

    public static function rules(): array
    {
        return [];
    }

    public function fireAutomation(Subscriber | Collection | QueryBuilder | EloquentBuilder | array $subscribers): void
    {
        if ($subscribers instanceof EloquentBuilder || $subscribers instanceof QueryBuilder) {
            $subscribers = $subscribers->cursor();
        }

        if ($subscribers instanceof Subscriber || is_array($subscribers)) {
            $subscribers = collect(Arr::wrap($subscribers))->lazy();
        }

        if ($subscribers instanceof Collection) {
            $subscribers = $subscribers->lazy();
        }

        $subscribers
            ->each(function (Subscriber $subscriber) {
                Automation::query()
                    ->where('status', AutomationStatus::STARTED)
                    ->where('email_list_id', $subscriber->email_list_id)
                    ->each(function (Automation $automation) use ($subscriber) {
                        if (get_class($automation->trigger) !== static::class) {
                            return;
                        }

                        if ($subscriber->inAutomation($automation)) {
                            return;
                        }

                        if (! $subscriber->isSubscribed()) {
                            return;
                        }

                        if (! $automation
                            ->newSubscribersQuery()
                            ->where("{$this->getSubscriberTableName()}.id", $subscriber->id)
                            ->count()) {
                            return;
                        }

                        $automation->run($subscriber);
                    });
            });
    }
}
