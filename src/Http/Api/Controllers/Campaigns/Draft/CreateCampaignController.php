<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns\Draft;

use Spatie\Mailcoach\Actions\Campaigns\CreateCampaignAction;
use Spatie\Mailcoach\Http\Api\Requests\CampaignRequest;
use Spatie\Mailcoach\Http\Api\Resources\CampaignResource;

class CreateCampaignController
{
    public function __invoke(
        CampaignRequest $request,
        CreateCampaignAction $createCampaignAction
    ) {
        $campaign = $createCampaignAction->execute($request->validated(), $request->template());

        return new CampaignResource($campaign);
    }
}
