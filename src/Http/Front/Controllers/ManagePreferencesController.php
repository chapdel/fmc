<?php

namespace Spatie\Mailcoach\Http\Front\Controllers;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class ManagePreferencesController
{
    use UsesMailcoachModels;

    public function show(string $subscriberUuid, string $sendUuid = null)
    {
        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
        if (! $subscriber = self::getSubscriberClass()::findByUuid($subscriberUuid)) {
            return view('mailcoach::landingPages.couldNotFindSubscription');
        }

        $emailList = $subscriber->emailList;

        if ($subscriber->status === SubscriptionStatus::UNSUBSCRIBED) {
            return view('mailcoach::landingPages.alreadyUnsubscribed', compact('emailList'));
        }

        $send = $subscriber->sends()->where('uuid', $sendUuid)->first();

        $tags = $emailList->tags()->where('type', TagType::DEFAULT)->where('visible_in_preferences', true)->get();

        if (! $tags->count()) {
            return view('mailcoach::landingPages.unsubscribe', compact('emailList', 'subscriber', 'send'));
        }

        return view('mailcoach::landingPages.manage-preferences', compact('emailList', 'subscriber', 'send', 'tags'));
    }

    public function confirm(Request $request, string $subscriberUuid, string $sendUuid = null)
    {
        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
        if (! $subscriber = self::getSubscriberClass()::findByUuid($subscriberUuid)) {
            return view('mailcoach::landingPages.couldNotFindSubscription');
        }

        $emailList = $subscriber->emailList;

        if ($subscriber->status === SubscriptionStatus::UNSUBSCRIBED) {
            return view('mailcoach::landingPages.alreadyUnsubscribed', compact('emailList'));
        }

        /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
        $send = self::getSendClass()::findByUuid($sendUuid ?? '');
        $tags = $emailList->tags()->where('type', TagType::DEFAULT)->where('visible_in_preferences', true)->get();

        if ($request->get('unsubscribe_from_all') || !$tags->count()) {
            $subscriber->unsubscribe($send);

            $emailList = $subscriber->emailList;

            return $emailList->redirect_after_unsubscribed
                ? redirect()->to($emailList->redirect_after_unsubscribed)
                : view('mailcoach::landingPages.unsubscribed', compact('emailList', 'subscriber'));
        }

        $subscriber->syncPreferenceTags(array_keys($request->get('tags', [])));

        $success = true;

        return view('mailcoach::landingPages.manage-preferences', compact('emailList', 'subscriber', 'send', 'tags', 'success'));
    }
}
