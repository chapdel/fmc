<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Http\App\Queries\EmailListSubscribersQuery;
use Spatie\SimpleExcel\SimpleExcelWriter;

class SubscribersExportController
{
    public function __invoke(EmailList $emailList)
    {
        $subscribersQuery = new EmailListSubscribersQuery($emailList);

        $subscriberCsv = SimpleExcelWriter::streamDownload("{$emailList->name} subscribers .csv");

        $subscribersQuery->each(function (Subscriber $subscriber) use ($subscriberCsv) {
            $this->resetMaximumExecutionTime();
            $subscriberCsv->addRow($subscriber->toExportRow());
        });

        $subscriberCsv->toBrowser();
    }

    protected function resetMaximumExecutionTime(): void
    {
        $maximumExecutionTime = (int)ini_get('max_execution_time');

        set_time_limit($maximumExecutionTime);
    }
}
