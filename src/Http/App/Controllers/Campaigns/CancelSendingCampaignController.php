<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns;

use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Enums\CampaignStatus;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class CancelSendingCampaignController
{
    use UsesMailcoachModels;

    public function __invoke(Campaign $campaign)
    {
        $batch = Bus::findBatch(
            $campaign->send_batch
        );

        $batch->cancel();

        $campaign->update([
            'status' => CampaignStatus::CANCELLED,
            'sent_at' => now(),
        ]);

        flash()->success(__('Sending successfully cancelled.'));

        return redirect()->back();
    }
}
