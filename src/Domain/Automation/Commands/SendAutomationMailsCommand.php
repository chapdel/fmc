<?php

namespace Spatie\Mailcoach\Domain\Automation\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Automation\Actions\SendAutomationMailsAction;
use Spatie\Mailcoach\Domain\Automation\Exceptions\SendAutomationMailsTimeLimitApproaching;
use Spatie\Mailcoach\Mailcoach;

class SendAutomationMailsCommand extends Command
{
    public $signature = 'mailcoach:send-automation-mails';

    public $description = 'Send pending automation mails.';

    public function handle()
    {
        /** @var \Spatie\Mailcoach\Domain\Automation\Actions\SendAutomationMailsAction $sendAutomationMailsAction */
        $sendAutomationMailsAction = Mailcoach::getAutomationActionClass('send_automation_mails_action', SendAutomationMailsAction::class);

        $maxRuntimeInSeconds = max(60, config('mailcoach.automation.send_automation_mails_maximum_job_runtime_in_seconds'));

        $stopExecutingAt = now()->addSeconds($maxRuntimeInSeconds);

        try {
            $sendAutomationMailsAction->execute($stopExecutingAt);
        } catch (SendAutomationMailsTimeLimitApproaching) {
            return;
        }
    }
}
