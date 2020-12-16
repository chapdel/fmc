<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions\Subscribers;

use Spatie\Mailcoach\Domain\Campaign\Actions\Subscribers\Concerns\SendsWelcomeMail;
use Spatie\Mailcoach\Domain\Campaign\Events\SubscribedEvent;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Spatie\Mailcoach\Domain\Campaign\Support\PendingSubscriber;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

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

        if ($pendingSubscriber->replaceTags) {
            $subscriber->syncTags($pendingSubscriber->tags);
        } elseif ($pendingSubscriber->tags) {
            $subscriber->addTags($pendingSubscriber->tags);
        }

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
