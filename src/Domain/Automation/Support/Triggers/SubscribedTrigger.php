<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Triggers;

use Spatie\Mailcoach\Domain\Automation\Models\Concerns\AutomationTrigger;
use Spatie\Mailcoach\Domain\Campaign\Events\SubscribedEvent;

class SubscribedTrigger extends AutomationTrigger
{
    public static function getName(): string
    {
        return __('When a user subscribes');
    }

    public function subscribe($events): void
    {
        $events->listen(
            SubscribedEvent::class,
            function ($event) {
                $this->fire($event->subscriber);
            }
        );
    }
}
