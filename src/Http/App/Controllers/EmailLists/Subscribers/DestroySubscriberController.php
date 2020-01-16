<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Subscriber;

class DestroySubscriberController
{
    public function __invoke(EmailList $emailList, Subscriber $subscriber)
    {
        $subscriber->delete();

        flash()->success("Subscriber {$subscriber->email} was deleted.");

        return back();
    }
}
