<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Spatie\Mailcoach\Models\EmailList;

class DestroyAllUnsubscribedController
{
    public function __invoke(EmailList $emailList)
    {
        $emailList->allSubscribers()->unsubscribed()->delete();

        flash()->success('All unsubscribes of the list have been deleted.');

        return back();
    }
}
