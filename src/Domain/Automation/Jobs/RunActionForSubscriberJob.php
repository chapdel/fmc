<?php

namespace Spatie\Mailcoach\Domain\Automation\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\AutomationAction;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class RunActionForSubscriberJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use UsesMailcoachModels;

    public Action $action;

    public Subscriber $subscriber;

    /** @var string */
    public $queue;

    public function __construct(Action $action, Subscriber $subscriber)
    {
        $this->action = $action;

        $this->subscriber = $subscriber;

        $this->queue = config('mailcoach.automation.perform_on_queue.run_action_for_subscriber_job');

        $this->connection = $this->connection ?? Config::getQueueConnection();
    }

    public function handle()
    {
        /** @var AutomationAction $action */
        $action = $this->action->action;

        /** @var ?Subscriber $subscriber */
        $subscriber = $this->action->subscribers()
            ->withPivot('run_at')
            ->find($this->subscriber->id);

        if (! $subscriber) {
            return;
        }

        if (is_null($subscriber->pivot->run_at)) {
            $action->run($subscriber);

            if ($action->shouldHalt($subscriber)) {
                $this->action->subscribers()->updateExistingPivot(
                    $subscriber,
                    ['halted_at' => now(), 'run_at' => now()],
                    touch: false
                );

                return;
            }

            if (! $action->shouldContinue($subscriber)) {
                return;
            }

            $this->action->subscribers()->updateExistingPivot(
                $subscriber,
                ['run_at' => now()],
                touch: false
            );
        }

        $nextActions = $action->nextActions($subscriber);
        if (count(array_filter($nextActions))) {
            foreach ($nextActions as $nextAction) {
                $nextAction->subscribers()->attach($subscriber);
            }
            $this->action->subscribers()->updateExistingPivot($subscriber, ['completed_at' => now()], touch: false);
        }
    }
}
