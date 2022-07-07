<div {{ $attributes->merge(['class' => 'text-gray-600 max-w-xl markup flex gap-3 items-baseline']) }}>
    <div class="flex-none -top-[0.2em]">
        <x-mailcoach::rounded-icon type="info" icon="fas fa-info" />   
    </div> 
    <div class="markup-links-dimmed">
        {{ $slot }}
    </div>
</div>
