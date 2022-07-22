<?php

namespace Spatie\Mailcoach\Http\App\Livewire;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class DashboardChart extends Component
{
    use UsesMailcoachModels;

    // Chart
    public string $start;
    public string $end;
    public Collection $stats;

    protected int $startSubscriptionsCount;
    protected int $startUnsubscribeCount;

    public function mount(): void
    {
        if (! isset($this->start)) {
            $firstSubscriber = self::getSubscriberClass()::subscribed()->orderBy('subscribed_at', 'asc')->first();

            if (! $firstSubscriber || $firstSubscriber->subscribed_at < now()->subMonths(2)) {
                $this->start = now()->subMonths(2)->format('Y-m-d');
            } elseif ($firstSubscriber->subscribed_at > now()->subWeeks(2)) {
                $this->start = now()->subWeeks(2)->format('Y-m-d');
            } else {
                $this->start = $firstSubscriber->subscribed_at->format('Y-m-d');
            }
        }

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
        $prefix = DB::getTablePrefix();
        $subscriberTable = $prefix . self::getSubscriberTableName() . (DB::connection() instanceof MySqlConnection ? ' USE INDEX (email_list_subscribed_index)' : '');

        $start = Date::parse($this->start)->startOfDay();
        $end = Date::parse($this->end)->endOfDay();


        $subscribes = DB::table(DB::raw($subscriberTable))
            ->selectRaw("count(*) as subscribed_count, DATE_FORMAT(subscribed_at, \"%Y-%m-%d\") as subscribed_day")
            ->whereBetween('subscribed_at', [$start, $end])
            ->whereNull('unsubscribed_at')
            ->orderBy('subscribed_day')
            ->groupBy('subscribed_day')
            ->get();

        if (! $subscribes->count()) {
            return collect();
        }

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
            $subscriberTotal -= (optional($unsubscribeCount)->unsubscribe_count ?? 0);

            return [
                'label' => Carbon::createFromFormat('Y-m-d', $result->subscribed_day)->startOfDay()->format('M d'),
                'subscribers' => $subscriberTotal,
                'subscribes' => $result->subscribed_count,
                'unsubscribes' => optional($unsubscribeCount)->unsubscribe_count,
            ];
        });

        $campaigns = self::getCampaignClass()::query()
            ->whereBetween('sent_at', [$start, $end])
            ->where('status', CampaignStatus::Sent)
            ->select(['id', 'name', 'sent_at'])
            ->get();

        $lastStats = [
            'subscribers' => $this->startSubscriptionsCount,
        ];

        return collect(CarbonPeriod::create($start, '1 day', $end))->map(function (CarbonInterface $day) use (
            $campaigns,
            $subscribers,
            &$lastStats
        ) {
            $day = $day->toImmutable();

            $label = $day->startOfDay()->format('M d');

            $stats = $subscribers->firstWhere('label', $label);

            if ($stats) {
                $lastStats = $stats;
            }

            $subscribers = $subscribers->firstWhere('label', $label) ?: [
                'label' => $label,
                'subscribers' => $lastStats['subscribers'] ?? 0,
                'subscribes' => $lastStats['subscribes'] ?? 0,
                'unsubscribes' => $lastStats['unsubscribes'] ?? 0,
            ];

            $subscribers['campaigns'] = $campaigns
                ->whereBetween('sent_at', [$day->startOfDay(), $day->endOfDay()])
                ->map(fn (Campaign $campaign) => ['id' => $campaign->id, 'name' => $campaign->name, 'sent_at' => $campaign->sent_at->format('M d')])
                ->toArray();

            return $subscribers;
        });
    }
}
