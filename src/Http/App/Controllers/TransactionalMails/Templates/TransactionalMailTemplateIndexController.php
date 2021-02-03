<?php


namespace Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\Templates;

use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\TransactionalMailTemplateQuery;

class TransactionalMailTemplateIndexController
{
    use UsesMailcoachModels;

    public function __invoke(TransactionalMailTemplateQuery $transactionalMailTemplateQuery)
    {
        return view('mailcoach::app.transactionalMail.templates.index', [
            'templates' => $transactionalMailTemplateQuery->paginate(),
            'templatesQuery' => $transactionalMailTemplateQuery,
            'templatesCount' => $this->getTransactionalMailTemplateClass()::count(),
        ]);
    }
}
