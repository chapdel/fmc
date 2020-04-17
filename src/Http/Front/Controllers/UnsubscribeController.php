<?php

namespace Spatie\Mailcoach\Http\Front\Controllers;

use Spatie\Mailcoach\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Traits\UsesSubscriber;

class UnsubscribeController
{
    use UsesSubscriber;

    public function __invoke(string $subscriberUuid, string $sendUuid = null)
    {
        /** @var \Spatie\Mailcoach\Models\Subscriber $subscriber */
        if (! $subscriber = $this->getSubscriberClass()::findByUuid($subscriberUuid)) {
            return view('mailcoach::landingPages.couldNotFindSubscription');
        }

        $emailList = $subscriber->emailList;

        if ($subscriber->status === SubscriptionStatus::UNSUBSCRIBED) {
            return view('mailcoach::landingPages.alreadyUnsubscribed', compact('emailList'));
        }

        /** @var \Spatie\Mailcoach\Models\Send $send */
        $send = Send::findByUuid($sendUuid ?? '');
        $subscriber->unsubscribe($send);

        $emailList = $subscriber->emailList;

        return $emailList->redirect_after_unsubscribed
            ? redirect()->to($emailList->redirect_after_unsubscribed)
            : view('mailcoach::landingPages.unsubscribed', compact('emailList', 'subscriber'));
    }
}
