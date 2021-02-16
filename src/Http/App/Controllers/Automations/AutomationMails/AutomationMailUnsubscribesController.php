<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Queries\AutomationMailUnsubscribesQuery;
use Spatie\Mailcoach\Http\App\Queries\CampaignUnsubscribesQuery;

class AutomationMailUnsubscribesController
{
    use AuthorizesRequests;

    public function __invoke(AutomationMail $mail)
    {
        $this->authorize('view', $mail);

        return view('mailcoach::app.automations.mails.unsubscribes', [
            'mail' => $mail,
            'unsubscribes' => (new AutomationMailUnsubscribesQuery($mail))->paginate(),
            'totalUnsubscribes' => $mail->unsubscribes()->count(),
        ]);
    }
}
