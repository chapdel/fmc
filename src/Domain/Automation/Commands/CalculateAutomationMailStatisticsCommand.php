<?php

namespace Spatie\Mailcoach\Domain\Automation\Commands;

use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Jobs\CalculateAutomationMailStatisticsJob;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Shared\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CalculateAutomationMailStatisticsCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:calculate-automation-mail-statistics {automationMailId?}';

    public $description = 'Calculate the statistics of automation mails';

    public function handle()
    {
        dispatch(new CalculateAutomationMailStatisticsJob($this->argument('automationMailId')));
    }
}
