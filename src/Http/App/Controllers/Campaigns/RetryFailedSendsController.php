<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns;

use Spatie\Mailcoach\Jobs\RetrySendingFailedSendsJob;
use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;

class RetryFailedSendsController
{
    public function __invoke(CampaignConcern $campaign)
    {
        $failedSendsCount = $campaign->sends()->failed()->count();

        if ($failedSendsCount === 0) {
            flash()->error("There are not failed mails to resend anymore.");

            return back();
        }

        dispatch(new RetrySendingFailedSendsJob($campaign));

        flash()->warning("Retrying to send ${failedSendsCount} mails...");

        return back();
    }
}
