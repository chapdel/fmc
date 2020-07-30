<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns;

use Spatie\Mailcoach\Actions\Campaigns\UpdateCampaignAction;
use Spatie\Mailcoach\Http\Api\Requests\CampaignRequest;
use Spatie\Mailcoach\Http\Api\Resources\CampaignResource;
use Spatie\Mailcoach\Http\App\Queries\CampaignsQuery;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class CampaignsController
{
    use UsesMailcoachModels;

    public function index(CampaignsQuery $campaigns)
    {
        return CampaignResource::collection($campaigns->paginate());
    }

    public function store(
        CampaignRequest $request,
        UpdateCampaignAction $createCampaignAction
    ) {
        $campaignClass = $this->getCampaignClass();

        $campaign = new $campaignClass;

        $campaign = $createCampaignAction->execute(
            $campaign,
            $request->validated(),
            $request->template()
        );

        return new CampaignResource($campaign);
    }

    public function update(CampaignRequest $request)
    {
    }
}
