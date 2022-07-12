<div {{ $attributes->merge(['class' => 'ml-2 max-w-xl']) }}>
    <div class="absolute -left-[8px] top-0 border-4 flex border-transparent rounded-full">
        <x-mailcoach::rounded-icon type="info" icon="fas fa-info" />   
    </div> 
    <div class="pl-7 text-gray-600 markup markup-links-dimmed">
        {{ $slot }}
    </div>
</div>
