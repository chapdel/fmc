<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Spatie\Mailcoach\Actions\Campaigns\CreateCampaignAction;
use Spatie\Mailcoach\Http\App\Requests\StoreCampaignRequest;

class CreateCampaignController
{
    public function __invoke(
        StoreCampaignRequest $request,
        CreateCampaignAction $createCampaignAction
    ) {
        $campaign = $createCampaignAction->execute(
            $request->validated(),
            $request->template()
        );

        flash()->success(__('Campaign :campaign was created.', ['campaign' => $campaign->name]));

        return redirect()->route('mailcoach.campaigns.settings', $campaign);
    }
}
