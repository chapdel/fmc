<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists;

use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\EmailListQuery;

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
