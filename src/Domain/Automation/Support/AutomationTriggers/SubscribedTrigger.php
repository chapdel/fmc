<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\AutomationTriggers;

use Spatie\Mailcoach\Domain\Campaign\Events\SubscribedEvent;

class SubscribedTrigger extends AutomationTrigger implements TriggeredByEvents
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
                $this->fireAutomation($event->subscriber);
            }
        );
    }
}
