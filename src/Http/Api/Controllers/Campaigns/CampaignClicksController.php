<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns;

use Spatie\Mailcoach\Http\Api\Resources\CampaignClickResource;
use Spatie\Mailcoach\Http\App\Queries\CampaignLinksQuery;
use Spatie\Mailcoach\Models\Campaign;

class CampaignClicksController
{
    public function __invoke(Campaign $campaign)
    {
        $campaignLinks = new CampaignLinksQuery($campaign);

        return CampaignClickResource::collection($campaignLinks->paginate());
    }
}
