<?php

namespace Spatie\Mailcoach\Domain\Automation\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\AutomationAction;
use Spatie\Mailcoach\Mailcoach;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class RunActionForActionSubscriberJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use UsesMailcoachModels;

    public function __construct(public ActionSubscriber $actionSubscriber)
    {
        $this->queue = config('mailcoach.automation.perform_on_queue.run_action_for_subscriber_job');

        $this->connection = $this->connection ?? Mailcoach::getQueueConnection();
    }

    public function uniqueId()
    {
        return $this->actionSubscriber->id;
    }

    public function handle(): void
    {
        if ($this->actionSubscriber->completed_at
            || $this->actionSubscriber->halted_at
            || ! $this->actionSubscriber->job_dispatched_at
        ) {
            return;
        }

        $subscriber = $this->actionSubscriber->subscriber;
        $subscriber->setRelation('pivot', $this->actionSubscriber);

        /** @var \Spatie\Mailcoach\Domain\Automation\Models\Automation $automation */
        $automation = $this->actionSubscriber->action->automation;

        if (! $automation->newSubscribersQuery()->where($this->getSubscriberTableName() . '.id', $subscriber->id)->exists()) {
            $this->actionSubscriber->update([
                'halted_at' => now(),
                'run_at' => now(),
                'job_dispatched_at' => null,
            ]);

            return;
        }

        /** @var AutomationAction $automationAction */
        $automationAction = $this->actionSubscriber->action->action;

        if (is_null($this->actionSubscriber->run_at)) {
            $automationAction->run($subscriber, $this->actionSubscriber);

            // Needed for the unsubscribe action
            $subscriber->refresh();

            if ($automationAction->shouldHalt($subscriber) || ! $subscriber->isSubscribed()) {
                $this->actionSubscriber->update([
                    'halted_at' => now(),
                    'run_at' => now(),
                    'job_dispatched_at' => null,
                ]);

                return;
            }

            if (! $automationAction->shouldContinue($subscriber)) {
                $this->actionSubscriber->update(['job_dispatched_at' => null]);

                return;
            }

            $this->actionSubscriber->update([
                'run_at' => now(),
                'job_dispatched_at' => null,
            ]);
        }

        if (is_null($this->actionSubscriber->completed_at)) {
            $nextActions = $automationAction->nextActions($subscriber);

            if (count(array_filter($nextActions))) {
                foreach ($nextActions as $nextAction) {
                    $nextAction->attachSubscriber($subscriber, $this->actionSubscriber);
                }

                $this->actionSubscriber->update(['completed_at' => now()]);
            }
        }

        $this->actionSubscriber->update(['job_dispatched_at' => null]);
    }
}
