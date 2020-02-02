<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Spatie\Mailcoach\Http\App\Queries\CampaignSendQuery;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Models\Subscriber;

class ReceivedCampaignsController
{
    public function __invoke(EmailList $emailList, Subscriber $subscriber)
    {
        $sendQuery = new CampaignSendQuery($subscriber);

        return view('mailcoach::app.emailLists.subscriber.receivedCampaigns', [
            'subscriber' => $subscriber,
            'sends' => $sendQuery->paginate(),
            'totalSendsCount' => Send::query()->where('subscriber_id', $subscriber->id)->count(),
        ]);
    }
}
