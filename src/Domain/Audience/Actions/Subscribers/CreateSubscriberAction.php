<?php

namespace Spatie\Mailcoach\Domain\Audience\Actions\Subscribers;

use Spatie\Mailcoach\Domain\Audience\Events\SubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Support\PendingSubscriber;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class CreateSubscriberAction
{
    use UsesMailcoachModels;

    public function execute(PendingSubscriber $pendingSubscriber): Subscriber
    {
        $subscriber = self::getSubscriberClass()::findForEmail($pendingSubscriber->email, $pendingSubscriber->emailList);

        $wasAlreadySubscribed = optional($subscriber)->isSubscribed();

        if (! $subscriber) {
            $subscriber = self::getSubscriberClass()::make([
                'email' => $pendingSubscriber->email,
                'email_list_id' => $pendingSubscriber->emailList->id,
            ]);
        }

        $subscriber->fill([
            'email' => $pendingSubscriber->email,
            'subscribed_at' => $subscriber->subscribed_at ?? now(),
            'unsubscribed_at' => null,
        ]);

        $subscriber->fill($pendingSubscriber->attributes);

        if (! $wasAlreadySubscribed) {
            if ($pendingSubscriber->emailList->requires_confirmation) {
                if ($pendingSubscriber->respectDoubleOptIn) {
                    $subscriber->subscribed_at = null;
                }
            }
        }

        $subscriber->save();

        if ($pendingSubscriber->replaceTags) {
            $subscriber->syncTags($pendingSubscriber->tags);
        } elseif ($pendingSubscriber->tags) {
            $subscriber->addTags($pendingSubscriber->tags);
        }

        if ($subscriber->isUnconfirmed()) {
            $sendConfirmSubscriberMailAction = Mailcoach::getAudienceActionClass('send_confirm_subscriber_mail', SendConfirmSubscriberMailAction::class);

            $sendConfirmSubscriberMailAction->execute($subscriber, $pendingSubscriber->redirectAfterSubscribed);
        }

        if ($subscriber->isSubscribed()) {
            event(new SubscribedEvent($subscriber));
        }

        return $subscriber->refresh();
    }
}
