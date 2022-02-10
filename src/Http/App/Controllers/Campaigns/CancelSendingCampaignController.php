<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CancelSendingCampaignController
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public function __invoke(Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $campaign->update([
            'status' => CampaignStatus::CANCELLED,
            'sent_at' => now(),
        ]);

        flash()->success(__('mailcoach - Sending successfully cancelled.'));

        return redirect()->back();
    }
}
