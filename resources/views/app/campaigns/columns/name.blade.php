@php($campaign = $getRecord())
<div class="fi-ta-text-item gap-1.5 px-3">
    <span class="link">{{ $campaign->name }}</span>
    @if ($campaign->sends_with_errors_count)
        <div class="flex items-center text-orange-500 text-xs mt-1">
            <x-mailcoach::rounded-icon type="warning" icon="fas fa-info" class="mr-1" />
            {{ $campaign->sends_with_errors_count }} {{ __mc_choice('failed send|failed sends', $campaign->sends_with_errors_count) }}
        </div>
    @endif
</div>
