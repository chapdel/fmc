<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Spatie\Mailcoach\Http\App\ViewModels\EmailListSummaryViewModel;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;

class SummaryController
{
    public function __invoke(EmailList $emailList)
    {
        $viewModel = new EmailListSummaryViewModel($emailList);

        return view('mailcoach::app.emailLists.summary', $viewModel);
    }
}
