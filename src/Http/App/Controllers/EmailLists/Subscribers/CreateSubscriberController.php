<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Spatie\Mailcoach\Http\App\Requests\CreateSubscriberRequest;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Subscriber;

class CreateSubscriberController
{
    public function store(
        EmailList $emailList,
        CreateSubscriberRequest $updateSubscriberRequest
    ) {
        Subscriber::createWithEmail($updateSubscriberRequest->email)
            ->withAttributes($updateSubscriberRequest->subscriberAttributes())
            ->skipConfirmation()
            ->subscribeTo($emailList);

        flash()->success("Subscriber {$updateSubscriberRequest->email} was created.");

        return back();
    }
}
