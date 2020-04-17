<?php

namespace Spatie\Mailcoach\Actions\Campaigns;

use Spatie\Mailcoach\Jobs\SendMailJob;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\Send;

class RetrySendingFailedSendsAction
{
    public function execute(Campaign $campaign): int
    {
        $failedSendsCount = $campaign->sends()->getQuery()->failed()->update([
            'sent_at' => null,
            'failed_at' => null,
            'failure_reason' => null,
        ]);

        $campaign->sends()->getQuery()->pending()->each(function (Send $pendingSend) {
            dispatch(new SendMailJob($pendingSend));
        });

        return $failedSendsCount;
    }
}
