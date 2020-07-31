<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers;

use Spatie\Mailcoach\Http\App\Queries\EmailListSubscribersQuery;
use Spatie\Mailcoach\Models\EmailList;

class SubscribersController
{
    public function index(EmailList $emailList)
    {
        $subscribers = new EmailListSubscribersQuery($emailList);

        return $subscribers->paginate();
    }
}
