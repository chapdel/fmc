<?php

namespace Spatie\Mailcoach\Domain\Automation\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailsJob;

class SendAutomationMailsCommand extends Command
{
    public $signature = 'mailcoach:send-automation-mails {--sync=false}';

    public $description = 'Send pending automation mails.';

    public function handle()
    {
        if ($this->option('sync')) {
            dispatch_sync(new SendAutomationMailsJob());

            return;
        }

        dispatch(new SendAutomationMailsJob());
    }
}
