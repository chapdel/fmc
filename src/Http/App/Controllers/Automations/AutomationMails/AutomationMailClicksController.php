<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Http\App\Queries\CampaignLinksQuery;

class AutomationMailClicksController
{
    use AuthorizesRequests;

    public function __invoke(AutomationMail $mail)
    {
        /** TODO */
       throw new Exception('not implemented yet');
    }
}
