<?php

namespace Spatie\Mailcoach\Commands;

use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Spatie\Mailcoach\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class CalculateStatisticsCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:calculate-statistics {campaignId?}';

    public $description = 'Calculate the statistics of the recently sent campaigns';

    protected CarbonInterface $now;

    public function handle()
    {
        $this->comment('Start calculating statistics...');

        $campaignId = $this->argument('campaignId');

        $campaignId
            ? dispatch_now(new CalculateStatisticsJob($this->getCampaignClass()::find($campaignId)))
            : $this->calculateStatisticsOfRecentCampaigns();

        $this->comment('All done!');
    }

    protected function calculateStatisticsOfRecentCampaigns(): void
    {
        $this->now = now();

        collect([
            [CarbonInterval::minute(0), CarbonInterval::minute(5), CarbonInterval::minute(0)],
            [CarbonInterval::minute(5), CarbonInterval::hour(2), CarbonInterval::minute(10)],
            [CarbonInterval::hour(2), CarbonInterval::day(), CarbonInterval::hour()],
            [CarbonInterval::day(), CarbonInterval::weeks(2), CarbonInterval::hour(4)],
        ])->eachSpread(function (CarbonInterval $startInterval, CarbonInterval $endInterval, CarbonInterval $recalculateThreshold) {
            $this
                ->findCampaignsWithStatisticsToRecalculate($startInterval, $endInterval, $recalculateThreshold)
                ->each(function (Campaign $campaign) {
                    $this->info("Calculating statistics for campaign id {$campaign->id}...");

                    $campaign->dispatchCalculateStatistics();
                });
        });
    }

    public function findCampaignsWithStatisticsToRecalculate(
        CarbonInterval $startInterval,
        CarbonInterval $endInterval,
        CarbonInterval $recalculateThreshold
    ): Collection {
        $periodEnd = $this->now->copy()->subtract($startInterval);
        $periodStart = $this->now->copy()->subtract($endInterval);

        return $this->getCampaignClass()::sentBetween($periodStart, $periodEnd)
            ->get()
            ->filter(function (Campaign $campaign) use ($periodEnd, $periodStart, $recalculateThreshold) {
                if (is_null($campaign->statistics_calculated_at)) {
                    return true;
                }

                $threshold = $this->now->copy()->subtract($recalculateThreshold);

                return $campaign->statistics_calculated_at->isBefore($threshold);
            });
    }
}
