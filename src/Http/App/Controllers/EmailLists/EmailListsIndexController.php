<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists;

use Spatie\Mailcoach\Http\App\Queries\EmailListQuery;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class EmailListsIndexController
{
    use UsesMailcoachModels;

    public function __invoke(EmailListQuery $emailListQuery)
    {
        return view('mailcoach::app.emailLists.index', [
            'emailLists' => $emailListQuery->paginate(),
            'totalEmailListsCount' => $this->getEmailListClass()::count(),
        ]);
    }
}
