<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Api\Resources\CampaignBounceResource;
use Spatie\Mailcoach\Http\App\Queries\CampaignBouncesQuery;

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
