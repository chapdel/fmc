<?php

namespace Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\Templates;

use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;

class EditTransactionalMailTemplateController
{
    public function __invoke(TransactionalMailTemplate $template)
    {
        return 'showing template';
    }
}
