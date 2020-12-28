<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Api\Resources\CampaignClickResource;
use Spatie\Mailcoach\Http\App\Queries\CampaignLinksQuery;

class CampaignClicksController
{
    public function __invoke(Campaign $campaign)
    {
        $campaignLinks = new CampaignLinksQuery($campaign);

        return CampaignClickResource::collection($campaignLinks->paginate());
    }
}
