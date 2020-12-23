<?php


namespace Spatie\Mailcoach\Domain\Automation\Models\Concerns;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\Concerns\AutomationStep;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;

abstract class AutomationTrigger extends AutomationStep
{
    public function trigger(Automation $automation): void
    {
    }

    public function subscribe($events): void
    {
    }

    public static function rules(): array
    {
        return [];
    }

    protected function fire(Subscriber|Collection|QueryBuilder|EloquentBuilder|array $subscribers): void
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

                        $automation->run($subscriber);
                    });
            });
    }
}
