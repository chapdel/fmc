<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers;

use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Requests\StoreSubscriberRequest;
use Spatie\Mailcoach\Http\Api\Resources\SubscriberResource;
use Spatie\Mailcoach\Http\App\Queries\EmailListSubscribersQuery;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class SubscribersController
{
    use UsesMailcoachModels, RespondsToApiRequests;

    public function index(EmailList $emailList)
    {
        $subscribers = new EmailListSubscribersQuery($emailList);

        return SubscriberResource::collection($subscribers->paginate());
    }

    public function show(Subscriber $subscriber)
    {
        return new SubscriberResource($subscriber);
    }

    public function store(StoreSubscriberRequest $request, EmailList $emailList)
    {
        /** @var \Spatie\Mailcoach\Support\PendingSubscriber $pendingSubscriber */
        $pendingSubscriber = $this
            ->getSubscriberClass()::createWithEmail($request->email)
            ->withAttributes($request->subscriberAttributes());

        if ($request->skip_confirmation) {
            $pendingSubscriber->skipConfirmation();
        }

        if ($request->skip_welcome_mail) {
            $pendingSubscriber->doNotSendWelcomeMail();
        }

        $subscriber = $pendingSubscriber->subscribeTo($emailList);

        return new SubscriberResource($subscriber);
    }

    public function destroy(Subscriber $subscriber)
    {
        $subscriber->delete();

        return $this->respondOk();
    }
}