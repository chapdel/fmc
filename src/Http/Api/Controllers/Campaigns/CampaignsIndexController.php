<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns;

use Spatie\Mailcoach\Http\Api\Resources\CampaignResource;
use Spatie\Mailcoach\Http\App\Queries\CampaignsQuery;

class CampaignsIndexController
{
    public function __invoke(CampaignsQuery $campaigns)
    {
        return CampaignResource::collection($campaigns->paginate());
    }
}
