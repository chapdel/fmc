<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Requests\SendTestEmailRequest;

class SendTestEmailController
{
    use AuthorizesRequests,
        RespondsToApiRequests;

    public function __invoke(SendTestEmailRequest $request, Campaign $campaign)
    {
        $this->authorize("view", $campaign);

        $campaign->sendTestMail($request->sanitizedEmails());

        return $this->respondOk();
    }
}
