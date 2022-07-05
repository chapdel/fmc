<div @if ($stats->count()) class="shadow-md bg-gray-50 p-6" @endif>
    @if ($stats->count())
        <h2 class="text-lg mb-4">
            <i class="fas fa-users mr-1"></i>
            {{ __('mailcoach - Audience') }}
        </h2>
        <div x-data="dashboardChart" x-init="renderChart({
            labels: @js($stats->pluck('label')->values()->toArray()),
            subscribers: @js($stats->pluck('subscribers')->values()->toArray()),
            subscribes: @js($stats->pluck('subscribes')->values()->toArray()),
            unsubscribes: @js($stats->pluck('unsubscribes')->values()->toArray()),
            campaigns: @js($stats->pluck('campaigns')->values()->toArray()),
        })">
            <canvas id="chart" style="position: relative; max-height:300px; width:100%; max-width: 100%;"></canvas>
        </div>
        <div class="text-right">
            <small class="text-gray-400">{{ __('mailcoach - You can drag the chart to zoom.') }}</small>
        </div>
    @endif
</div>
