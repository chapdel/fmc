<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\CouldNotSendCampaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Requests\SendCampaignRequest;

class SendCampaignController
{
    use AuthorizesRequests;
    use RespondsToApiRequests;

    public function __invoke(SendCampaignRequest $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        try {
            $campaign->send();
        } catch (CouldNotSendCampaign $exception) {
            abort(400, $exception->getMessage());
        }

        return $this->respondOk();
    }
}
