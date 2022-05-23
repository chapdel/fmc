<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Http\App\Requests\Campaigns\UpdateCampaignContentRequest;

class AutomationMailContentController
{
    use AuthorizesRequests;

    public function edit(AutomationMail $automationMail)
    {
        $this->authorize('update', $automationMail);

        return view("mailcoach::app.automations.mails.content", [
            'mail' => $automationMail,
        ]);
    }
}
