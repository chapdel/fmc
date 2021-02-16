<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Http\App\Queries\EmailListSubscribersQuery;

class SubscribersIndexController
{
    use AuthorizesRequests;

    public function __invoke(EmailList $emailList)
    {
        $this->authorize('view', $emailList);

        $subscribersQuery = new EmailListSubscribersQuery($emailList);

        return view('mailcoach::app.emailLists.subscribers.index', [
            'subscribers' => $subscribersQuery->paginate(),
            'emailList' => $emailList,
            'totalSubscriptionsCount' => $emailList->subscribers()->count(),
            'activeFilter' => request()->get('filter')['status'] ?? '',
        ]);
    }
}
