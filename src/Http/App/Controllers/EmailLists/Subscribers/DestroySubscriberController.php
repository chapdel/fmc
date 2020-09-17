<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Spatie\Mailcoach\Actions\Subscribers\DeleteSubscriberAction;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Support\Config;

class DestroySubscriberController
{
    public function __invoke(EmailList $emailList, Subscriber $subscriber)
    {
        /** @var DeleteSubscriberAction $deleteSubscriberAction */
        $deleteSubscriberAction = Config::getActionClass('delete_subscriber', DeleteSubscriberAction::class);

        $deleteSubscriberAction->execute($subscriber);

        flash()->success(__('Subscriber :subscriber was deleted.', ['subscriber' => $subscriber->email]));

        return back();
    }
}
