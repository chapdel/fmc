<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class RetrySendingFailedSendsAction
{
    public function execute(Campaign $campaign): int
    {
        return $campaign->contentItem->sends()->getQuery()->failed()->update([
            'sent_at' => null,
            'failed_at' => null,
            'failure_reason' => null,
        ]);
    }
}
