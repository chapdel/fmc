<div>
    <div x-data="campaignStatisticsChart" x-init="renderChart({
        labels: @js($stats->pluck('label')->values()->toArray()),
        opens: @js($stats->pluck('opens')->values()->toArray()),
        clicks: @js($stats->pluck('clicks')->values()->toArray()),
    })">
        <canvas id="chart" style="position: relative; max-height:300px; width:100%; max-width: 100%;"></canvas>
    </div>
    <div class="text-right">
        <small class="text-gray-400">You can drag the chart to zoom.</small>
    </div>
</div>
