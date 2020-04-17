<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Carbon\CarbonInterval;
use Spatie\Mailcoach\Http\App\Requests\SendTestEmailRequest;
use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;

class SendTestEmailController
{
    public function __invoke(CampaignConcern $campaign, SendTestEmailRequest $request)
    {
        if (! $campaign->isPending()) {
            flash()->error("Can't send a test email for campaign {$campaign->name} because it has already been sent.");

            return back();
        }

        cache()->put('mailcoach-test-email-addresses', $request->emails, CarbonInterval::month()->totalSeconds);

        $campaign->sendTestMail($request->sanitizedEmails());

        if (count($request->sanitizedEmails()) > 1) {
            $emailCount = count($request->sanitizedEmails());

            flash()->success("A test email was sent to {$emailCount} addresses.");
        } else {
            flash()->success("A test email was sent to {$request->sanitizedEmails()[0]}.");
        }

        return back();
    }
}
