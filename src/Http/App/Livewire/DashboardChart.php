<?php

namespace Spatie\Mailcoach\Http\App\Livewire;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class DashboardChart extends Component
{
    use UsesMailcoachModels;

    // Chart
    public string $start;
    public string $end;
    public Collection $stats;

    public function mount(): void
    {
        $this->start ??= now()->subMonths(2)->format('Y-m-d');
        $this->end ??= now()->format('Y-m-d');
    }

    public function updatedStart($newStart): void
    {
        if (Date::parse($newStart) > Date::parse($this->end)) {
            $this->start = $this->end;
        }
    }

    public function updatedEnd($newEnd): void
    {
        if (Date::parse($newEnd) > Date::now()) {
            $this->end = Date::now()->format('Y-m-d');
        }

        if (Date::parse($newEnd) < Date::parse($this->start)) {
            $this->end = $this->start;
        }
    }

    public function render()
    {
        $this->startSubscriptionsCount = self::getSubscriberClass()::subscribed()
            ->where('subscribed_at', '<', $this->start)
            ->count();

        $this->startUnsubscribeCount = self::getSubscriberClass()::query()
            ->unsubscribed()
            ->where('unsubscribed_at', '>', $this->start)
            ->count();

        $this->stats = $this->createStats();

        return view('mailcoach::app.partials.dashboard-chart');
    }

    protected function createStats(): Collection
    {
        $subscriberTable = $this->getSubscriberTableName() . ' USE INDEX (email_list_subscribed_index)';

        $start = Date::parse($this->start)->startOfDay();
        $end = Date::parse($this->end)->endOfDay();

        $subscribes = DB::table(DB::raw($subscriberTable))
            ->selectRaw("count(*) as subscribed_count, DATE_FORMAT(subscribed_at, \"%Y-%m-%d\") as subscribed_day")
            ->whereBetween('subscribed_at', [$start, $end])
            ->whereNull('unsubscribed_at')
            ->orderBy('subscribed_day')
            ->groupBy('subscribed_day')
            ->get();

        $unsubscribes = DB::table(DB::raw($subscriberTable))
            ->selectRaw("count(*) as unsubscribe_count, DATE_FORMAT(unsubscribed_at, \"%Y-%m-%d\") as unsubscribe_day")
            ->whereBetween('unsubscribed_at', [$start, $end])
            ->whereNotNull('unsubscribed_at')
            ->orderBy('unsubscribe_day')
            ->groupBy('unsubscribe_day')
            ->get();

        $subscriberTotal = $this->startSubscriptionsCount;
        $subscribers = collect($subscribes)->map(function ($result) use (&$subscriberTotal, $unsubscribes) {
            $subscriberTotal += $result->subscribed_count;
            $unsubscribeCount = $unsubscribes->where('unsubscribe_day', $result->subscribed_day)->first();

            return [
                'label' => Carbon::createFromFormat('Y-m-d', $result->subscribed_day)->startOfDay()->format('M d'),
                'subscribers' => $subscriberTotal,
                'subscribes' => $result->subscribed_count,
                'unsubscribes' => optional($unsubscribeCount)->unsubscribe_count,
            ];
        });

        $lastStats = [];

        return collect(CarbonPeriod::create($start, '1 day', $end))->map(function (CarbonInterface $day) use (
            $subscribers,
            &$lastStats
        ) {
            $label = $day->startOfDay()->format('M d');

            $stats = $subscribers->firstWhere('label', $label);

            if ($stats) {
                $lastStats = $stats;
            }

            return $subscribers->firstWhere('label', $label) ?: [
                'label' => $label,
                'subscribers' => $lastStats['subscribers'] ?? 0,
                'unsubscribes' => $lastStats['unsubscribes'] ?? 0,
            ];
        });
    }
}
