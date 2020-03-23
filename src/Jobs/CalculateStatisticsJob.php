<?php

namespace Spatie\Mailcoach\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Actions\Campaigns\CalculateStatisticsAction;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Support\CalculateStatisticsLock;
use Spatie\Mailcoach\Support\Config;

class CalculateStatisticsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Campaign $campaign;

    public $queue;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;

        $this->queue = config('mailcoach.perform_on_queue.calculate_statistics_job');

        $this->connection = $this->connection ?? Config::getQueueConnection();
    }

    public function handle()
    {
        try {
            /** @var \Spatie\Mailcoach\Actions\Campaigns\CalculateStatisticsAction $calculateStatistics */
            $calculateStatistics = Config::getActionClass('calculate_statistics', CalculateStatisticsAction::class);

            $calculateStatistics->execute($this->campaign);
        } catch (Exception $exception) {
            report($exception);
        }

        (new CalculateStatisticsLock($this->campaign))->release();
    }
}
