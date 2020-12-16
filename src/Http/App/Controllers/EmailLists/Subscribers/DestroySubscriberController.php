<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Spatie\Mailcoach\Domain\Campaign\Actions\Subscribers\DeleteSubscriberAction;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Support\Config;

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
