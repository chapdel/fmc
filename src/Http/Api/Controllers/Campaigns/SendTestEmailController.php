<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Requests\SendTestEmailRequest;

class SendTestEmailController
{
    use RespondsToApiRequests;

    public function __invoke(SendTestEmailRequest $request, Campaign $campaign)
    {
        $campaign->sendTestMail($request->sanitizedEmails());

        return $this->respondOk();
    }
}
