<?php

namespace Spatie\Mailcoach\Domain\Campaign\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

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
