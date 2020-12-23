<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Triggers;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Automation\Models\Concerns\AutomationTrigger;
use Spatie\Mailcoach\Domain\Campaign\Events\SubscribedEvent;

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
