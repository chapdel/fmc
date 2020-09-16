<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Spatie\Mailcoach\Actions\Campaigns\UpdateCampaignAction;
use Spatie\Mailcoach\Http\App\Requests\StoreCampaignRequest;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class CreateCampaignController
{
    use UsesMailcoachModels;

    public function __invoke(
        StoreCampaignRequest $request,
        UpdateCampaignAction $updateCampaignAction
    ) {
        $campaignClass = $this->getCampaignClass();

        $campaign = new $campaignClass;

        $campaign = $updateCampaignAction->execute(
            $campaign,
            $request->validated(),
            $request->template()
        );

        flash()->success(__('Campaign :campaign was created.', ['campaign' => $campaign->name]));

        return redirect()->route('mailcoach.campaigns.settings', $campaign);
    }
}
