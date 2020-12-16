<?php

namespace Spatie\Mailcoach\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Enums\AutomationStatus;
use Spatie\Mailcoach\Models\Action;
use Spatie\Mailcoach\Models\Automation;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class RunAutomationActionsCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:run-automation-actions';

    public $description = 'Run all registered actions for automations';

    public function handle()
    {
        $this->comment('Start running actions...');

        Automation::query()
            ->whereHas('actions')
            ->where('status', AutomationStatus::STARTED)
            ->cursor()
            ->each(function (Automation $automation) {
                $automation->actions->each(function (Action $action) {
                    $action->run();
                });
            });

        $this->comment('All done!');
    }
}
