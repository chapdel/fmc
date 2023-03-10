<?php

namespace Spatie\Mailcoach\Domain\Settings\Enums;

use Spatie\Mailcoach\Domain\Audience\Events\SubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\TagAddedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\TagRemovedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\UnconfirmedSubscriberCreatedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\UnsubscribedEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;

enum WebhookEventTypes
{
    case Subscribed;
    case UnconfirmedSubscriberCreated;
    case Unsubscribed;
    case CampaignSent;
    case TagAdded;
    case TagRemoved;

    public function label(): string
    {
        return match ($this) {
            self::Subscribed => 'Subscribed',
            self::UnconfirmedSubscriberCreated => 'Unconfirmed subscriber created',
            self::Unsubscribed => 'Unsubscribed',
            self::CampaignSent => 'Campaign sent',
            self::TagAdded => 'Tag added',
            self::TagRemoved => 'Tag removed',
        };
    }

    public function value(): string
    {
        return match ($this) {
            self::Subscribed => class_basename(SubscribedEvent::class),
            self::UnconfirmedSubscriberCreated => class_basename(UnconfirmedSubscriberCreatedEvent::class),
            self::Unsubscribed => class_basename(UnsubscribedEvent::class),
            self::CampaignSent => class_basename(CampaignSentEvent::class),
            self::TagAdded => class_basename(TagAddedEvent::class),
            self::TagRemoved => class_basename(TagRemovedEvent::class),
        };
    }
}
