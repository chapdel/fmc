<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns;

use Spatie\Mailcoach\Actions\Campaigns\SendCampaignAction;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Requests\SendCampaignRequest;
use Spatie\Mailcoach\Models\Campaign;

class SendCampaignController
{
    use RespondsToApiRequests;

    public function __invoke(SendCampaignRequest $request, Campaign $campaign, SendCampaignAction $sendCampaignAction)
    {
        $campaign->send();

        return $this->respondOk();
    }
}
