<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Api\Resources\CampaignOpenResource;
use Spatie\Mailcoach\Http\App\Queries\CampaignOpensQuery;

class CampaignOpensController
{
    public function __invoke(Campaign $campaign)
    {
        $campaignOpens = new CampaignOpensQuery($campaign);

        return CampaignOpenResource::collection($campaignOpens->paginate());
    }
}
