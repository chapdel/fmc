<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;

class AutomationMailOutboxController
{
    use AuthorizesRequests;

    public function __invoke(AutomationMail $mail)
    {
        /** TODO */
        throw new Exception('not implemented yet');
    }
}
