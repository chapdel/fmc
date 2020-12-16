<?php

namespace Spatie\Mailcoach\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Enums\AutomationStatus;
use Spatie\Mailcoach\Models\Automation;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class RunAutomationTriggersCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:run-automation-triggers';

    public $description = 'Run all triggers for automations';

    public function handle()
    {
        $this->comment('Start running triggers...');

        Automation::query()
            ->whereHas('actions')
            ->where('status', AutomationStatus::STARTED)
            ->cursor()
            ->each(function (Automation $automation) {
                $automation->trigger->trigger($automation);
            });

        $this->comment('All done!');
    }
}
