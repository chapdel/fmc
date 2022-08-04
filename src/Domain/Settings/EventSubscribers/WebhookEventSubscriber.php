<?php

namespace Spatie\Mailcoach\Domain\Settings\EventSubscribers;

use Spatie\Mailcoach\Domain\Audience\Events\SubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\UnconfirmedSubscriberCreatedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\UnsubscribedEvent;
use Spatie\Mailcoach\Domain\Settings\Actions\SendWebhookAction;
use Spatie\Mailcoach\Http\Api\Resources\SubscriberResource;
use Spatie\Mailcoach\Mailcoach;

class WebhookEventSubscriber
{
    public function handSubscribedEvent(SubscribedEvent $event)
    {
        $emailList = $event->subscriber->emailList;

        $payload = SubscriberResource::make($event->subscriber)
            ->toArray(request());

        $this->sendWebhookAction()->execute($emailList, $payload, $event);
    }

    public function handUnsubscribedEvent(UnsubscribedEvent $event)
    {
        $emailList = $event->subscriber->emailList;

        $payload = SubscriberResource::make($event->subscriber)
            ->toArray(request());

        $this->sendWebhookAction()->execute($emailList, $payload, $event);
    }

    public function handeUnconfirmedSubscriberCreatedEvent(UnconfirmedSubscriberCreatedEvent $event)
    {
        $emailList = $event->subscriber->emailList;

        $payload = SubscriberResource::make($event->subscriber)
            ->toArray(request());

        $this->sendWebhookAction()->execute($emailList, $payload, $event);
    }

    protected function sendWebhookAction(): SendWebhookAction
    {
        /** @var $action SendWebhookAction */
        $action = Mailcoach::getSharedActionClass('send_webhook', SendWebhookAction::class);

        return $action;
    }

    public function subscribe(): array
    {
        return [
            SubscribedEvent::class => 'handSubscribedEvent',
            UnconfirmedSubscriberCreatedEvent::class => 'handeUnconfirmedSubscriberCreatedEvent',
            UnsubscribedEvent::class => 'handUnsubscribedEvent',

        ];
    }
}
