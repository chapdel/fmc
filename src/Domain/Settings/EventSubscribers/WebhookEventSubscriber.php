<?php

namespace Spatie\Mailcoach\Domain\Settings\EventSubscribers;

use Spatie\Mailcoach\Domain\Audience\Events\SubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\TagAddedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\TagRemovedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\UnconfirmedSubscriberCreatedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\UnsubscribedEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Settings\Actions\SendWebhookAction;
use Spatie\Mailcoach\Http\Api\Resources\CampaignResource;
use Spatie\Mailcoach\Http\Api\Resources\SubscriberResource;
use Spatie\Mailcoach\Mailcoach;

class WebhookEventSubscriber
{
    public function subscribe(): array
    {
        return [
            SubscribedEvent::class => 'handleSubscribedEvent',
            UnconfirmedSubscriberCreatedEvent::class => 'handleUnconfirmedSubscriberCreatedEvent',
            UnsubscribedEvent::class => 'handleUnsubscribedEvent',
            CampaignSentEvent::class => 'handleCampaignSent',
            TagAddedEvent::class => 'handleTagAddedEvent',
            TagRemovedEvent::class => 'handleTagRemovedEvent',
        ];
    }

    public function handleSubscribedEvent(SubscribedEvent $event)
    {
        $emailList = $event->subscriber->emailList;

        $payload = SubscriberResource::make($event->subscriber)
            ->toArray(request());

        $this->sendWebhookAction()->execute($emailList, $payload, $event);
    }

    public function handleUnsubscribedEvent(UnsubscribedEvent $event)
    {
        $emailList = $event->subscriber->emailList;

        $payload = SubscriberResource::make($event->subscriber)
            ->toArray(request());

        $this->sendWebhookAction()->execute($emailList, $payload, $event);
    }

    public function handleUnconfirmedSubscriberCreatedEvent(UnconfirmedSubscriberCreatedEvent $event)
    {
        $emailList = $event->subscriber->emailList;

        $payload = SubscriberResource::make($event->subscriber)
            ->toArray(request());

        $this->sendWebhookAction()->execute($emailList, $payload, $event);
    }

    public function handleCampaignSent(CampaignSentEvent $event)
    {
        $emailList = $event->campaign->emailList;

        $payload = CampaignResource::make($event->campaign)->toArray(request());

        $this->sendWebhookAction()->execute($emailList, $payload, $event);
    }

    public function handleTagAddedEvent(TagAddedEvent $event)
    {
        $emailList = $event->subscriber->emailList;
        $tag = $event->tag;

        $payload = array_merge(
            SubscriberResource::make($event->subscriber)->toArray(request()),
            ['added_tag' => $tag->name]
        );

        $this->sendWebhookAction()->execute($emailList, $payload, $event);
    }

    public function handleTagRemovedEvent(TagRemovedEvent $event)
    {
        $emailList = $event->subscriber->emailList;
        $tag = $event->tag;

        $payload = array_merge(
            SubscriberResource::make($event->subscriber)->toArray(request()),
            ['removed_tag' => $tag->name]
        );

        $this->sendWebhookAction()->execute($emailList, $payload, $event);
    }

    protected function sendWebhookAction(): SendWebhookAction
    {
        /** @var SendWebhookAction $action */
        $action = Mailcoach::getSharedActionClass('send_webhook', SendWebhookAction::class);

        return $action;
    }
}
