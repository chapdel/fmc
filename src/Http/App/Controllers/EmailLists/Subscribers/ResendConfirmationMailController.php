<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Spatie\Mailcoach\Domain\Campaign\Actions\Subscribers\SendConfirmSubscriberMailAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;

class ResendConfirmationMailController
{
    public function __invoke(Subscriber $subscriber, SendConfirmSubscriberMailAction $sendConfirmSubscriberMailAction)
    {
        $sendConfirmSubscriberMailAction->execute($subscriber);

        flash()->success(__('A confirmation mail has been sent to :subscriber', ['subscriber' => $subscriber->email]));

        return back();
    }
}
