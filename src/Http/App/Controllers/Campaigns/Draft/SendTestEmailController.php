<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Carbon\CarbonInterval;
use Spatie\Mailcoach\Http\App\Requests\SendTestEmailRequest;
use Spatie\Mailcoach\Models\Campaign;

class SendTestEmailController
{
    public function __invoke(Campaign $campaign, SendTestEmailRequest $request)
    {
        if (! $campaign->isPending()) {
            flash()->error(__('Can\'t send a test email for campaign :campaign because it has already been sent.', ['campaign' => $campaign->name]));

            return back();
        }

        cache()->put('mailcoach-test-email-addresses', $request->emails, CarbonInterval::month()->totalSeconds);

        $campaign->sendTestMail($request->sanitizedEmails());

        if (count($request->sanitizedEmails()) > 1) {
            $emailCount = count($request->sanitizedEmails());

            flash()->success(__('A test email was sent to :count addresses.', ['count' => $emailCount]));
        } else {
            flash()->success(__('A test email was sent to :email.', ['email' => $request->sanitizedEmails()[0]]));
        }

        return back();
    }
}
