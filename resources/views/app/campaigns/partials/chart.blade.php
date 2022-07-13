<div>
    @if ($stats->count())
        <div x-data="campaignStatisticsChart" x-init="renderChart({
            labels: @js($stats->pluck('label')->values()->toArray()),
            opens: @js($stats->pluck('opens')->values()->toArray()),
            clicks: @js($stats->pluck('clicks')->values()->toArray()),
        })">
            <canvas id="chart" style="position: relative; max-height:300px; width:100%; max-width: 100%;"></canvas>
        </div>
    @endif
    <div class="mt-4 text-right">
        <small class="text-gray-500">You can drag the chart to zoom.</small>
    </div>
</div>
