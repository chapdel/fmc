<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Spatie\Mailcoach\Actions\Subscribers\SendConfirmSubscriberMailAction;
use Spatie\Mailcoach\Models\Subscriber;

class ResendConfirmationMailController
{
    public function __invoke(Subscriber $subscriber, SendConfirmSubscriberMailAction $sendConfirmSubscriberMailAction)
    {
        $sendConfirmSubscriberMailAction->execute($subscriber);

        flash()->success("A confirmation mail has been sent to {$subscriber->email}");

        return back();
    }
}
