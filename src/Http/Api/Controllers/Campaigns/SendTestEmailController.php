<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns;

use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Requests\SendTestEmailRequest;
use Spatie\Mailcoach\Models\Campaign;

class SendTestEmailController
{
    use RespondsToApiRequests;

    public function __invoke(SendTestEmailRequest $request, Campaign $campaign)
    {
        $campaign->sendTestMail($request->sanitizedEmails());

        return $this->respondOk();
    }
}
