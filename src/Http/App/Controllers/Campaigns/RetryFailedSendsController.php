<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns;

use Spatie\Mailcoach\Domain\Campaign\Jobs\RetrySendingFailedSendsJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class RetryFailedSendsController
{
    public function __invoke(Campaign $campaign)
    {
        $failedSendsCount = $campaign->sends()->failed()->count();

        if ($failedSendsCount === 0) {
            flash()->error(__('There are not failed mails to resend anymore.'));

            return back();
        }

        dispatch(new RetrySendingFailedSendsJob($campaign));

        flash()->warning(__('Retrying to send :failedSendsCount mails...', ['failedSendsCount' => $failedSendsCount]));

        return back();
    }
}
