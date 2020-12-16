<?php

namespace Spatie\Mailcoach\Domain\Campaign\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Campaign\Actions\CalculateStatisticsAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Support\CalculateStatisticsLock;
use Spatie\Mailcoach\Domain\Shared\Support\Config;

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
            /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\CalculateStatisticsAction $calculateStatistics */
            $calculateStatistics = Config::getActionClass('calculate_statistics', CalculateStatisticsAction::class);

            $calculateStatistics->execute($this->campaign);
        } catch (Exception $exception) {
            report($exception);
        }

        (new CalculateStatisticsLock($this->campaign))->release();
    }
}
