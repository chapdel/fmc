<?php

namespace Spatie\Mailcoach\Domain\Automation\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Shared\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class RunAutomationActionsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use UsesMailcoachModels;

    public int $uniqueFor = 60;

    public function handle()
    {
        static::getAutomationClass()::query()
            ->where('status', AutomationStatus::Started)
            ->lazyById()
            ->each(function (Automation $automation) {
                if (! is_null($automation->run_at) && $automation->run_at->add($automation->interval)->isFuture()) {
                    return;
                }

                $automation->allActions()->each(function (Action $action) {
                    return dispatch(new RunAutomationActionJob($action));
                });

                $automation->update(['run_at' => now()]);
            });
    }
}
