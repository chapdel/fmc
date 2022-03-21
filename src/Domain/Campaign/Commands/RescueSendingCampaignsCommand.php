<?php

namespace Spatie\Mailcoach\Domain\Campaign\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

/** @deprecated */
class RescueSendingCampaignsCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:rescue-sending-campaigns';

    public $description = 'Rescue sending campaigns.';

    public function handle()
    {
        $this->comment('No longer necessary, but we keep this as removing would break applications...');
    }
}
