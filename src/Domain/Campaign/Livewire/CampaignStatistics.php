<?php

namespace Spatie\Mailcoach\Domain\Campaign\Livewire;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Support\Svg\BezierCurve;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CampaignStatistics extends Component
{
    use UsesMailcoachModels;

    public Campaign $campaign;

    // Chart
    public Collection $stats;

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function render(): View
    {
        $this->stats = $this->createStats();

        return view('mailcoach::app.campaigns.partials.chart');
    }

    protected function createStats(): Collection
    {
        if (! $this->campaign->wasAlreadySent()) {
            return collect();
        }

        $start = $this->campaign->sent_at->startOfHour()->toImmutable();

        if ($this->campaign->open_count > 0) {
            $firstOpenCreatedAt = $this->campaign->opens()->first()->created_at;

            if ($firstOpenCreatedAt < $start) {
                $start = $firstOpenCreatedAt->startOfHour()->toImmutable();
            }
        }

        $end = $this->campaign->opens()->latest()->first('created_at')?->created_at
            ?? $start->addHours(24);

        $campaignOpenTable = self::getCampaignOpenTableName();
        $campaignClickTable = self::getCampaignClickTableName();
        $campaignLinkTable = self::getCampaignLinkTableName();

        $opensPerHour = DB::table($campaignOpenTable)
            ->where('campaign_id', $this->campaign->id)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d %H") as hour, COUNT(*) as opens')
            ->groupBy('hour')
            ->get();

        $clicksPerHour = DB::table($campaignClickTable)
            ->join($campaignLinkTable, 'campaign_link_id', '=', $campaignLinkTable . '.id')
            ->where('campaign_id', $this->campaign->id)
            ->selectRaw("DATE_FORMAT({$campaignClickTable}.created_at, \"%Y-%m-%d %H\") as hour, COUNT(*) as clicks")
            ->groupBy('hour')
            ->get();

        return collect(CarbonPeriod::create($start, '1 hour', $end))->map(function (CarbonInterface $hour) use ($opensPerHour, $clicksPerHour) {
            return [
                'label' => $hour->isoFormat('dd HH:mm'),
                'opens' => $opensPerHour->where('hour', $hour->format('Y-m-d H'))->first()?->opens ?? 0,
                'clicks' => $clicksPerHour->where('hour', $hour->format('Y-m-d H'))->first()?->clicks ?? 0,
            ];
        });
    }

    public function averageOpenRate(): float
    {
        return $this->emailList->campaigns()->average('open_rate') / 100;
    }

    public function averageClickRate(): float
    {
        return $this->emailList->campaigns()->average('click_rate') / 100;
    }

    public function averageUnsubscribeRate(): float
    {
        return $this->emailList->campaigns()->average('unsubscribe_rate') / 100;
    }

    public function averageBounceRate(): float
    {
        return $this->emailList->campaigns()->average('bounce_rate') / 100;
    }

    public function growthRate(): float
    {
        if ($this->startSubscriptionsCount === 0) {
            return 0;
        }

        // Percent Change = 100 × (Present or Future Value – Past or Present Value) / Past or Present Value
        return round(100 * ($this->totalSubscriptionsCount - $this->startSubscriptionsCount) / $this->startSubscriptionsCount, 2);
    }

    public function churnRate(): float
    {
        if ($this->totalSubscriptionsCount === 0) {
            return 0;
        }

        return round($this->startUnsubscribeCount / $this->totalSubscriptionsCount, 2);
    }

    public function totalSubscriptionsCount(): int
    {
        return $this->totalSubscriptionsCount;
    }

    public function startSubscriptionsCount(): int
    {
        return $this->startSubscriptionsCount;
    }

    public function totalUnsubscribeCount(): int
    {
        return $this->totalUnsubscribeCount;
    }

    public function startUnsubscribeCount(): int
    {
        return $this->startUnsubscribeCount;
    }
}
