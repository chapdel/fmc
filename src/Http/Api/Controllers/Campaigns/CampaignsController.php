<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns;

use Spatie\Mailcoach\Actions\Campaigns\UpdateCampaignAction;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Requests\CampaignRequest;
use Spatie\Mailcoach\Http\Api\Resources\CampaignResource;
use Spatie\Mailcoach\Http\App\Queries\CampaignsQuery;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class CampaignsController
{
    use UsesMailcoachModels,
        RespondsToApiRequests;

    public function index(CampaignsQuery $campaigns)
    {
        return CampaignResource::collection($campaigns->paginate());
    }

    public function store(
        CampaignRequest $request,
        UpdateCampaignAction $updateCampaignAction
    ) {
        $campaignClass = $this->getCampaignClass();

        $campaign = new $campaignClass;

        $campaign = $updateCampaignAction->execute(
            $campaign,
            $request->validated(),
            $request->template()
        );

        return new CampaignResource($campaign);
    }

    public function update(
        Campaign $campaign,
        CampaignRequest $request,
        UpdateCampaignAction $updateCampaignAction
    ) {
        $campaign = $updateCampaignAction->execute(
            $campaign,
            $request->validated(),
        );

        return new CampaignResource($campaign);
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();

        return $this->respondOk();
    }
}
