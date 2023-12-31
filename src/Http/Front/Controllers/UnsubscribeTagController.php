<?php

namespace Spatie\Mailcoach\Http\Front\Controllers;

use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class UnsubscribeTagController
{
    use UsesMailcoachModels;

    public function show(string $subscriberUuid, string $tag, ?string $sendUuid = null)
    {
        /** @var Subscriber $subscriber */
        $subscriber = $this->getSubscriberClass()::findByUuid($subscriberUuid);

        if (! $subscriber) {
            return view('mailcoach::landingPages.couldNotFindSubscription');
        }

        $emailList = $subscriber->emailList;

        if ($subscriber->status === SubscriptionStatus::Unsubscribed) {
            return view('mailcoach::landingPages.alreadyUnsubscribed', compact('emailList'));
        }

        $send = $subscriber->sends()->where('uuid', $sendUuid)->first();

        return view('mailcoach::landingPages.unsubscribe-tag', compact('emailList', 'subscriber', 'tag', 'send'));
    }

    public function confirm(string $subscriberUuid, string $tag)
    {
        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
        $subscriber = $this->getSubscriberClass()::findByUuid($subscriberUuid);

        if (! $subscriber) {
            return view('mailcoach::landingPages.couldNotFindSubscription');
        }

        $emailList = $subscriber->emailList;

        if ($subscriber->status === SubscriptionStatus::Unsubscribed) {
            return view('mailcoach::landingPages.alreadyUnsubscribed', compact('emailList'));
        }

        $subscriber->removeTag(urldecode($tag));

        $emailList = $subscriber->emailList;

        return $emailList->redirect_after_unsubscribed
            ? redirect()->to($emailList->redirect_after_unsubscribed)
            : view('mailcoach::landingPages.unsubscribed-tag', compact('emailList', 'subscriber', 'tag'));
    }
}
