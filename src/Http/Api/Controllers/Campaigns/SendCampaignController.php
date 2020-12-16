<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns;

use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Requests\SendCampaignRequest;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class SendCampaignController
{
    use RespondsToApiRequests;

    public function __invoke(SendCampaignRequest $request, Campaign $campaign)
    {
        $campaign->send();

        return $this->respondOk();
    }
}
