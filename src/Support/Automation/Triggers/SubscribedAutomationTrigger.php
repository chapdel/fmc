<?php

namespace Spatie\Mailcoach\Support\Automation\Triggers;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Events\SubscribedEvent;
use Spatie\Mailcoach\Models\Concerns\AutomationTrigger;

class SubscribedAutomationTrigger extends AutomationTrigger
{
    public static function getName(): string
    {
        return __('When a user subscribes');
    }

    public function handleSubscribed(SubscribedEvent $event)
    {
        $this->fire($event->subscriber);
    }

    public function subscribe($events): void
    {
        $events->listen(
            SubscribedEvent::class,
            self::class.'@handleSubscribed',
        );
    }

    public static function make(array $data): self
    {
        return new self();
    }

    public static function createFromRequest(Request $request): AutomationTrigger
    {
        return new self();
    }
}
