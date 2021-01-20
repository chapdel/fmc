<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;

class DestroyAllUnsubscribedController
{
    use AuthorizesRequests;

    public function __invoke(EmailList $emailList)
    {
        $this->authorize('update', $emailList);

        $emailList->allSubscribers()->unsubscribed()->delete();

        flash()->success(__('All unsubscribers of the list have been deleted.'));

        return back();
    }
}
