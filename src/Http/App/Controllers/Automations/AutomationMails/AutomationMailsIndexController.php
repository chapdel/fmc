<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\AutomatedMailQuery;

class AutomationMailsIndexController
{
    use AuthorizesRequests,
        UsesMailcoachModels;

    public function __invoke(AutomatedMailQuery $automatedMailQuery)
    {
        $this->authorize("viewAny", AutomationMail::class);
        ray(AutomationMail::get());

        return view('mailcoach::app.automations.mails.index', [
            'mails' => ray()->pass($automatedMailQuery->paginate()),
            'mailsQuery' => $automatedMailQuery,
            'totalMailsCount' => ray()->pass($this->getAutomationMailClass()::count()),
        ]);
    }
}
