<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Spatie\Mailcoach\Http\App\Requests\CreateSubscriberRequest;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class CreateSubscriberController
{
    use UsesMailcoachModels;

    public function store(
        EmailList $emailList,
        CreateSubscriberRequest $updateSubscriberRequest
    ) {
        $this->getSubscriberClass()::createWithEmail($updateSubscriberRequest->email)
            ->withAttributes($updateSubscriberRequest->subscriberAttributes())
            ->skipConfirmation()
            ->subscribeTo($emailList);

        flash()->success(__('Subscriber :subscriber was created.', ['subscriber' => $updateSubscriberRequest->email]));

        return back();
    }
}
