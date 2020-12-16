<?php

namespace Spatie\Mailcoach\Http\Front\Controllers;

use Spatie\Mailcoach\Domain\Campaign\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Send;
use Spatie\Mailcoach\Domain\Support\Traits\UsesMailcoachModels;

class UnsubscribeController
{
    use UsesMailcoachModels;

    public function show(string $subscriberUuid, string $sendUuid = null)
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Subscriber $subscriber */
        if (! $subscriber = $this->getSubscriberClass()::findByUuid($subscriberUuid)) {
            return view('mailcoach::landingPages.couldNotFindSubscription');
        }

        $emailList = $subscriber->emailList;

        if ($subscriber->status === SubscriptionStatus::UNSUBSCRIBED) {
            return view('mailcoach::landingPages.alreadyUnsubscribed', compact('emailList'));
        }

        return view('mailcoach::landingPages.unsubscribe', compact('emailList', 'subscriber'));
    }

    public function confirm(string $subscriberUuid, string $sendUuid = null)
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Subscriber $subscriber */
        if (! $subscriber = $this->getSubscriberClass()::findByUuid($subscriberUuid)) {
            return view('mailcoach::landingPages.couldNotFindSubscription');
        }

        $emailList = $subscriber->emailList;

        if ($subscriber->status === SubscriptionStatus::UNSUBSCRIBED) {
            return view('mailcoach::landingPages.alreadyUnsubscribed', compact('emailList'));
        }

        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Send $send */
        $send = Send::findByUuid($sendUuid ?? '');
        $subscriber->unsubscribe($send);

        $emailList = $subscriber->emailList;

        return $emailList->redirect_after_unsubscribed
            ? redirect()->to($emailList->redirect_after_unsubscribed)
            : view('mailcoach::landingPages.unsubscribed', compact('emailList', 'subscriber'));
    }
}
