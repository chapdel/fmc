<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns;

use Spatie\Mailcoach\Http\Api\Resources\CampaignUnsubscribeResource;
use Spatie\Mailcoach\Http\App\Queries\CampaignUnsubscribesQuery;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class CampaignUnsubscribesController
{
    public function __invoke(Campaign $campaign)
    {
        $unsubscribes = (new CampaignUnsubscribesQuery($campaign));

        return CampaignUnsubscribeResource::collection($unsubscribes->paginate());
    }
}
