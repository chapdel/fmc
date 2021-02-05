<?php

namespace Spatie\Mailcoach\Domain\Automation\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

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
                $this->info("Triggering automation id `{$automation->id}`");

                /** @var \Spatie\Mailcoach\Domain\Automation\Support\AutomationTriggers\AutomationTrigger $trigger */
                $trigger = $automation->trigger;

                $trigger->trigger($automation);
            });

        $this->comment('All done!');
    }
}
