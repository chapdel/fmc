<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Api\Queries\CampaignBouncesQuery;
use Spatie\Mailcoach\Http\Api\Resources\CampaignBounceResource;

class CampaignBouncesController
{
    use AuthorizesRequests;

    public function __invoke(Campaign $campaign)
    {
        $this->authorize('view', $campaign);

        $query = new CampaignBouncesQuery($campaign);

        return CampaignBounceResource::collection($query->paginate());
    }
}
