<?php

namespace Spatie\Mailcoach\Domain\Campaign\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailJob;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class RetryPendingSendsCommand extends Command
{
    public $signature = 'mailcoach:retry-pending-sends';

    public $description = 'Dispatch a job for each MailSend that has not been sent yet';

    public function handle()
    {
        $pendingSendCount = Send::whereNull('sent_at')->count();

        $this->comment("Dispatching jobs for {$pendingSendCount} pending Sends");

        Send::whereNull('sent_at')->each(function (Send $send) {
            dispatch(new SendCampaignMailJob($send));
        });

        $this->comment('All done!');
    }
}
