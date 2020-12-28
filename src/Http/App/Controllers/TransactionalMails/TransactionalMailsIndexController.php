<?php

namespace Spatie\Mailcoach\Http\App\Controllers\TransactionalMails;

use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\TransactionalMailsQuery;

class TransactionalMailsIndexController
{
    use UsesMailcoachModels;

    public function __invoke(TransactionalMailsQuery $transactionalMailsQuery)
    {
        return view('mailcoach::app.transactionalMails.index', [
            'transactionalMails' => $transactionalMailsQuery->paginate(),
            'transactionalMailsQuery' => $transactionalMailsQuery,
            'transactionalMailsCount' => $this->getTransactionalMailClass()::count(),
        ]);
    }
}
