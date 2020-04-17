<?php

namespace Spatie\Mailcoach\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Actions\Campaigns\RetrySendingFailedSendsAction;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Support\Config;

class RetrySendingFailedSendsJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Campaign $campaign;

    /** @var string */
    public $queue;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;

        $this->queue = config('mailcoach.perform_on_queue.send_campaign_job');

        $this->connection = $this->connection ?? Config::getQueueConnection();
    }

    public function handle()
    {
        /** @var \Spatie\Mailcoach\Actions\Campaigns\RetrySendingFailedSendsAction $retrySendingFailedSendsAction */
        $retrySendingFailedSendsAction = Config::getActionClass('retry_sending_failed_sends', RetrySendingFailedSendsAction::class);

        $retrySendingFailedSendsAction->execute($this->campaign);
    }
}
