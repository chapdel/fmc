<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Spatie\Mailcoach\Http\App\Queries\EmailListSubscribersQuery;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\SimpleExcel\SimpleExcelWriter;

class SubscribersExportController
{
    public function __invoke(EmailList $emailList)
    {
        $subscribersQuery = new EmailListSubscribersQuery($emailList);

        $subscriberCsv = SimpleExcelWriter::streamDownload("{$emailList->name} subscribers .csv");

        $subscribersQuery->each(function (Subscriber $subscriber) use ($subscriberCsv) {
            $subscriberCsv->addRow($subscriber->toExportRow());
        });

        $subscriberCsv->toBrowser();
    }
}
