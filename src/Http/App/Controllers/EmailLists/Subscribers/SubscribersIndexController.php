<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Http\App\Queries\EmailListSubscribersQuery;

class SubscribersIndexController
{
    public function __invoke(EmailList $emailList)
    {
        $subscribersQuery = new EmailListSubscribersQuery($emailList);

        return view('mailcoach::app.emailLists.subscribers', [
            'subscribers' => $subscribersQuery->paginate(),
            'emailList' => $emailList,
            'totalSubscriptionsCount' => $emailList->subscribers()->count(),
            'activeFilter' => request()->get('filter')['status'] ?? '',
        ]);
    }
}
