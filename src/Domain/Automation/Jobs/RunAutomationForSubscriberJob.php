<?php

namespace Spatie\Mailcoach\Domain\Automation\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class RunAutomationForSubscriberJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use UsesMailcoachModels;

    public $deleteWhenMissingModels = true;

    public Automation $automation;

    public Subscriber $subscriber;

    /** @var string */
    public $queue;

    public function __construct(Automation $automation, Subscriber $subscriber)
    {
        $this->automation = $automation;

        $this->subscriber = $subscriber;

        $this->queue = config('mailcoach.automation.perform_on_queue.run_automation_for_subscriber_job');

        $this->connection = $this->connection ?? Config::getQueueConnection();
    }

    public function handle()
    {
        if ($this->automation->status !== AutomationStatus::STARTED) {
            return;
        }

        if ($this->subscriber->inAutomation($this->automation)) {
            return;
        }

        if (! $this->subscriber->isSubscribed()) {
            return;
        }

        if (! $this->automation
            ->newSubscribersQuery()
            ->where("{$this->getSubscriberTableName()}.id", $this->subscriber->id)
            ->count()
        ) {
            return;
        }

        $this->automation->run($this->subscriber);
    }

    public function retryUntil()
    {
        return now()->addHours(config('mailcoach.campaigns.throttling.retry_until_hours', 24));
    }
}
