{{-- md:max-w-xs md:max-w-sm md:max-w-md md:max-w-lg md:max-w-xl md:max-w-2xl md:max-w-3xl md:max-w-4xl md:max-w-5xl --}}
<div {{ $attributes->merge(['class' => 'ml-2 alert alert-info ' . (isset($full) ? '' : 'md:max-w-' . ($maxWidth ?? 'xl'))]) }}>
    <div class="absolute -left-[8px] -top-[3px] border-4 flex border-white rounded-full">
        <x-mailcoach::rounded-icon type="info" icon="{{ isset($sync) ? 'fas fa-sync fa-spin' : 'fas fa-info' }}" />
    </div>
    <div class="markup markup-links-dimmed">
        {{ $slot }}
    </div>
</div>
