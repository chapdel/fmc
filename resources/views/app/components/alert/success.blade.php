<div {{ $attributes->merge(['class' => 'alert alert-success ' . (isset($full) ? '' : 'md:max-w-xl')]) }}>
    <div class="absolute -left-[8px] -top-[6px] border-2 flex border-white rounded-full">
        <x-mailcoach::rounded-icon type="success" icon="fas fa-check" />
    </div>
    <div class="markup markup-links-dimmed">
        {{ $slot }}
    </div>
</div>
