<?php

namespace Spatie\Mailcoach\Domain\Automation\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Trigger;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\TriggeredBySchedule;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class RunAutomationTriggersJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use UsesMailcoachModels;

    public int $uniqueFor = 60;

    public function __construct()
    {
        $this->onQueue(config('mailcoach.perform_on_queue.schedule'));
        $this->connection ??= Mailcoach::getQueueConnection();
    }

    public function handle()
    {
        static::getAutomationTriggerClass()::query()
            ->whereHas('automation', function (Builder $query) {
                $query
                    ->whereHas('actions')
                    ->where('status', AutomationStatus::Started);
            })
            ->with('automation')
            ->lazyById()
            ->each(function (Trigger $trigger) {
                /** @var \Spatie\Mailcoach\Domain\Automation\Support\Triggers\AutomationTrigger $automationTrigger */
                $automationTrigger = $trigger->trigger;

                if (! $automationTrigger instanceof TriggeredBySchedule) {
                    return;
                }

                $automationTrigger
                    ->setAutomation($trigger->automation)
                    ->trigger();
            });
    }
}
