<?php

namespace Spatie\Mailcoach\Actions\Subscribers;

use Spatie\Mailcoach\Actions\Subscribers\Concerns\SendsWelcomeMail;
use Spatie\Mailcoach\Events\SubscribedEvent;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Support\Config;
use Spatie\Mailcoach\Support\PendingSubscriber;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class CreateSubscriberAction
{
    use SendsWelcomeMail, UsesMailcoachModels;

    public function execute(PendingSubscriber $pendingSubscriber): Subscriber
    {
        $subscriber = $this->getSubscriberClass()::findForEmail($pendingSubscriber->email, $pendingSubscriber->emailList);

        $wasAlreadySubscribed = optional($subscriber)->isSubscribed();

        if (! $subscriber) {
            $subscriber = $this->getSubscriberClass()::make([
                'email' => $pendingSubscriber->email,
                'email_list_id' => $pendingSubscriber->emailList->id,
            ]);
        }

        $subscriber->fill([
            'subscribed_at' => now(),
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

        $subscriber->syncTags($pendingSubscriber->tags);

        if ($subscriber->isUnconfirmed()) {
            $sendConfirmSubscriberMailAction = Config::getActionClass('send_confirm_subscriber_mail', SendConfirmSubscriberMailAction::class);

            $sendConfirmSubscriberMailAction->execute($subscriber, $pendingSubscriber->redirectAfterSubscribed);
        }

        if ($subscriber->isSubscribed()) {
            if ($pendingSubscriber->sendWelcomeMail && ! $wasAlreadySubscribed) {
                $this->sendWelcomeMail($subscriber);
            }

            event(new SubscribedEvent($subscriber));
        }

        return $subscriber->refresh();
    }
}
