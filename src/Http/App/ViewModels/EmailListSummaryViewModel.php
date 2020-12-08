<?php

namespace Spatie\Mailcoach\Http\App\ViewModels;

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Support\Svg\BezierCurve;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;
use Spatie\ViewModels\ViewModel;

class EmailListSummaryViewModel extends ViewModel
{
    use UsesMailcoachModels;

    protected CarbonImmutable $start;

    protected EmailList $emailList;

    protected Collection $stats;

    protected int $subscribersLimit;

    protected int $totalSubscriptionsCount;

    protected int $startSubscriptionsCount;

    protected int $totalUnsubscribeCount;

    protected int $startUnsubscribeCount;

    public function __construct(EmailList $emailList)
    {
        $this->start = now()->subDays(29)->startOfDay()->toImmutable();

        $this->emailList = $emailList;

        $this->totalSubscriptionsCount = $this->emailList->subscribers()->count();

        $this->totalUnsubscribeCount = $this->emailList->allSubscribers()->unsubscribed()->count();

        $this->startSubscriptionsCount = $this->emailList->subscribers()
            ->where('subscribed_at', '<', $this->start)
            ->count();

        $this->startUnsubscribeCount = $this->emailList->allSubscribers()->unsubscribed()->where('unsubscribed_at', '>', $this->start)->count();

        $this->stats = $this->createStats();

        $this->subscribersLimit = (ceil($this->stats->max('subscribers') * 1.1 / 10) * 10) ?: 1;
    }

    public function activeFilter(): string
    {
        return request()->get('filter')['status'] ?? '';
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

    public function emailList(): EmailList
    {
        return $this->emailList;
    }

    public function stats(): Collection
    {
        return $this->stats;
    }

    public function subscribersLimit(): int
    {
        return $this->subscribersLimit;
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

        $unsubscribesSinceStart = $this->emailList
            ->allSubscribers()
            ->unsubscribed()
            ->where('unsubscribed_at', '>', $this->start)
            ->count();

        return round($unsubscribesSinceStart / $this->totalSubscriptionsCount, 2);
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

    public function subscribersPath(): string
    {
        $points = $this->stats
            ->pluck('subscribers')
            ->map(function (int $subscribers, int $index) {
                return [$index, 100 - ($subscribers / $this->subscribersLimit) * 100];
            })
            ->toArray();

        return (new BezierCurve([[0, 100], ...$points, [30,100]]))->toPath();
    }

    protected function createStats(): Collection
    {
        $subscriberTotal = $this->startSubscriptionsCount;

        $subscribes = DB::table($this->getSubscriberTableName())
            ->selectRaw("count(*) as subscribed_count, date(subscribed_at) as subscribed_day")
            ->where('email_list_id', $this->emailList->id)
            ->where('subscribed_at', '>=', $this->start)
            ->whereNull('unsubscribed_at')
            ->groupBy('subscribed_day')
            ->get();

        $unsubscribes = DB::table($this->getSubscriberTableName())
            ->selectRaw("count(*) as unsubscribe_count, date(unsubscribed_at) as unsubscribe_day")
            ->where('email_list_id', $this->emailList->id)
            ->where('unsubscribed_at', '>=', $this->start)
            ->whereNotNull('unsubscribed_at')
            ->groupBy('unsubscribe_day')
            ->get();

        return collect($subscribes)->map(function ($result) use (&$subscriberTotal, $unsubscribes) {
            $subscriberTotal += $result->subscribed_count;
            $unsubscribeCount = $unsubscribes->where('unsubscribe_day', $result->subscribed_day)->first();

            return [
                'label' => Carbon::createFromFormat('Y-m-d', $result->subscribed_day)->format('M d'),
                'subscribers' => $subscriberTotal,
                'unsubscribes' => optional($unsubscribeCount)->unsubscribe_count,
            ];
        });
    }
}
