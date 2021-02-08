<?php

namespace Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\Templates;

use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;

class TransactionalMailSettingsController
{
    public function edit(TransactionalMailTemplate $template)
    {
        return view('mailcoach::app.transactionalMails.templates.settings', compact('template'));
    }

    public function update()
    {
    }
}
